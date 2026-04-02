"""
BloodBridge XGBoost Retraining Module
======================================
Loads real response history from the database, trains a new XGBoost donor
acceptance model, saves the artifacts, and returns a metrics dict ready to
be stored in model_training_logs.

Called by the FastAPI /api/retrain endpoint.
"""

import os
import sys
import json
import pickle
import logging
import numpy as np
import pandas as pd
from datetime import datetime

from sqlalchemy import create_engine, text
from sklearn.model_selection import train_test_split
from sklearn.metrics import (
    accuracy_score, precision_score, recall_score,
    roc_auc_score, f1_score,
)
import xgboost as xgb

# Allow importing from the ai_service root (config.py lives there)
sys.path.insert(0, os.path.join(os.path.dirname(__file__), '..'))
from config import (
    MODEL_PATH, FEATURES_PATH,
    DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD,
)

logger = logging.getLogger(__name__)

# These must stay in sync with get_donor_features() in api/routes.py
FEATURE_NAMES = [
    'total_responses', 'days_since_last', 'total_donations',
    'age', 'gender', 'blood_type',
    'urgency_level', 'distance_km', 'hour_of_day', 'day_of_week',
    'acceptance_rate', 'recency_score', 'loyalty_score',
]

# Augment with synthetic rows when real data is below this threshold
MIN_REAL_RECORDS = 50

XGBOOST_PARAMS = {
    'max_depth':          4,
    'learning_rate':      0.1,
    'n_estimators':       100,
    'subsample':          0.8,
    'colsample_bytree':   0.8,
    'use_label_encoder':  False,
    'eval_metric':        'logloss',
    'random_state':       42,
}


# =============================================================================
# Database helpers
# =============================================================================

def _make_engine():
    url = f'mysql+pymysql://{DB_USER}:{DB_PASSWORD}@{DB_HOST}:{DB_PORT}/{DB_NAME}'
    return create_engine(url, pool_pre_ping=True)


def _load_real_data(engine) -> pd.DataFrame:
    """
    One row per terminal response (ACCEPTED, DECLINED, COMPLETED,
    IGNORED, NO_SHOW). Features are the donor's full history snapshot
    as of the response date (avoids data leakage from future responses).
    label = 1 if COMPLETED or ACCEPTED, else 0.
    """
    query = text("""
        SELECT
            rr.id                                                                AS response_id,
            CASE WHEN rr.status IN (1, 3) THEN 1 ELSE 0 END                     AS label,

            /* History BEFORE this response */
            COUNT(h.id)                                                          AS total_responses,
            COALESCE(DATEDIFF(rr.responded_at, MAX(h.responded_at)), 999)       AS days_since_last,
            COALESCE(dhp.total_donations, 0)                                     AS total_donations,

            /* Donor profile */
            TIMESTAMPDIFF(YEAR, d.birth_date, rr.responded_at)                  AS age,
            CASE d.gender WHEN 'male' THEN 0 ELSE 1 END                         AS gender,
            COALESCE(dhp.blood_type, 0)                                          AS blood_type,

            COUNT(CASE WHEN h.status IN (1, 3) THEN 1 END)                      AS accepted_count,
            COUNT(CASE WHEN h.status = 5      THEN 1 END)                       AS no_show_count,

            /* Request context */
            br.urgency_level                                                     AS urgency_level,
            10.0                                                                 AS distance_km,
            HOUR(rr.responded_at)                                                AS hour_of_day,
            WEEKDAY(rr.responded_at)                                             AS day_of_week

        FROM request_responses rr
        JOIN donors           d   ON  d.id  = rr.donor_id AND d.deleted_at IS NULL
        JOIN blood_requests   br  ON  br.id = rr.blood_request_id
        LEFT JOIN donor_health_profiles dhp ON dhp.donor_id = d.id
        LEFT JOIN request_responses     h   ON  h.donor_id         = d.id
                                             AND h.id               < rr.id
                                             AND h.responded_at     < rr.responded_at
                                             AND h.status           IN (1,2,3,4,5,6,7)
                                             AND h.responded_at     IS NOT NULL

        WHERE rr.status      IN (1, 2, 3, 4, 5)
          AND rr.responded_at IS NOT NULL

        GROUP BY
            rr.id, rr.status, rr.responded_at,
            d.birth_date, d.gender,
            dhp.total_donations, dhp.blood_type,
            br.urgency_level
    """)

    with engine.connect() as conn:
        df = pd.read_sql(query, conn)

    return df


# =============================================================================
# Synthetic data augmentation
# =============================================================================

def _generate_synthetic(n: int, seed: int = 42) -> pd.DataFrame:
    """
    Mirrors the distribution used in 03_dataset_generation.ipynb.
    Only called when real records are fewer than MIN_REAL_RECORDS.
    """
    rng = np.random.default_rng(seed)

    total_responses = rng.integers(0, 21, n)
    days_since_last = np.where(
        total_responses == 0, 999,
        rng.exponential(scale=60, size=n).clip(0, 365).astype(int),
    )
    total_donations = np.round(
        rng.beta(a=2, b=5, size=n) * 10
    ).clip(0, 10).astype(int)

    age         = rng.normal(loc=33, scale=5, size=n).clip(18, 65).astype(int)
    gender      = rng.choice([0, 1], size=n, p=[0.75, 0.25])
    blood_type  = rng.integers(1, 9, n)
    urgency     = rng.choice([1, 2], size=n, p=[0.70, 0.30])
    distance_km = rng.exponential(scale=8, size=n).clip(0.5, 50)
    hour_of_day = rng.integers(0, 24, n)
    day_of_week = rng.integers(0, 7, n)

    total_clipped   = np.clip(total_responses, 1, None)
    accepted_count  = np.round(
        rng.beta(2, 3, n) * total_clipped
    ).clip(0, total_clipped).astype(int)
    no_show_count   = np.round(
        rng.beta(1, 5, n) * total_clipped
    ).clip(0, total_clipped).astype(int)

    no_show_penalty = no_show_count
    adjusted_total  = total_clipped + no_show_penalty
    acceptance_rate = accepted_count / adjusted_total
    recency_score   = np.exp(-days_since_last / 60)
    loyalty_score   = np.clip(total_donations / 10, 0, 1)

    # Acceptance probability mirrors the notebook's weighted formula
    base = 0.47
    p    = np.full(n, base)
    p   += acceptance_rate * 0.40
    p   += np.where(days_since_last == 999, -0.15,
           np.where(days_since_last <= 7,   +0.20,
           np.where(days_since_last <= 30,  +0.10,
           np.where(days_since_last <= 90,  +0.05, -0.10))))
    p   += np.where(urgency == 2, +0.15, -0.05)
    p   += np.where(distance_km < 3,  +0.15,
           np.where(distance_km < 8,  +0.05,
           np.where(distance_km < 15, +0.00,
           np.where(distance_km < 30, -0.10, -0.20))))
    p   += np.where((hour_of_day >= 8) & (hour_of_day <= 20), +0.05, -0.10)
    p   += np.where(total_donations >= 5, +0.10,
           np.where(total_donations >= 2, +0.05, 0))
    p   += np.where((age >= 25) & (age <= 45), +0.05,
           np.where(age > 55, -0.05, 0))
    p    = np.clip(p, 0.05, 0.95)
    label = (rng.random(n) < p).astype(int)

    return pd.DataFrame({
        'total_responses': total_responses,
        'days_since_last': days_since_last,
        'total_donations': total_donations,
        'age':             age,
        'gender':          gender,
        'blood_type':      blood_type,
        'urgency_level':   urgency,
        'distance_km':     distance_km,
        'hour_of_day':     hour_of_day,
        'day_of_week':     day_of_week,
        'acceptance_rate': acceptance_rate,
        'recency_score':   recency_score,
        'loyalty_score':   loyalty_score,
        'label':           label,
    })


# =============================================================================
# Feature engineering (mirrors api/routes.py get_donor_features)
# =============================================================================

def _engineer_features(df: pd.DataFrame) -> pd.DataFrame:
    df = df.copy()

    df['total_responses'] = df['total_responses'].fillna(0).astype(int)
    df['days_since_last'] = df['days_since_last'].fillna(999).astype(int)
    df['total_donations'] = df['total_donations'].fillna(0).astype(int)
    df['age']             = df['age'].fillna(30).astype(int)
    df['gender']          = df['gender'].fillna(0).astype(int)
    df['blood_type']      = df['blood_type'].fillna(0).astype(int)
    df['accepted_count']  = df.get('accepted_count', pd.Series(0, index=df.index)).fillna(0).astype(int)
    df['no_show_count']   = df.get('no_show_count',  pd.Series(0, index=df.index)).fillna(0).astype(int)

    total_clipped   = df['total_responses'].clip(lower=1)
    adjusted_total  = total_clipped + df['no_show_count']
    df['acceptance_rate'] = df['accepted_count'] / adjusted_total
    df['recency_score']   = np.exp(-df['days_since_last'] / 60)
    df['loyalty_score']   = (df['total_donations'] / 10).clip(0, 1)

    return df


# =============================================================================
# Public API
# =============================================================================

def retrain() -> dict:
    """
    Train a new XGBoost model on the current DB state.

    Returns a dict with keys suitable for inserting into model_training_logs:
        model_version, data_records_used, real_records_used,
        hyperparameters, metrics, feature_importance
    """
    engine = _make_engine()

    # ── Load real data ────────────────────────────────────────────────────────
    df_real = _load_real_data(engine)
    real_count = len(df_real)
    logger.info(f"Loaded {real_count} real response records for training.")

    if real_count > 0:
        df_real = _engineer_features(df_real)
        df_real = df_real[FEATURE_NAMES + ['label']].dropna()

    # ── Augment with synthetic data if real data is sparse ───────────────────
    if real_count < MIN_REAL_RECORDS:
        n_synthetic = max(MIN_REAL_RECORDS, 300) - real_count
        logger.info(f"Augmenting with {n_synthetic} synthetic rows.")
        df_synth = _generate_synthetic(n_synthetic)
        df = pd.concat(
            [df_real[FEATURE_NAMES + ['label']], df_synth[FEATURE_NAMES + ['label']]],
            ignore_index=True,
        ) if real_count > 0 else df_synth[FEATURE_NAMES + ['label']]
    else:
        df = df_real[FEATURE_NAMES + ['label']]

    total_records = len(df)
    logger.info(f"Training on {total_records} total records ({real_count} real + {total_records - real_count} synthetic).")

    # ── Train / test split ────────────────────────────────────────────────────
    X = df[FEATURE_NAMES].values
    y = df['label'].values

    X_train, X_test, y_train, y_test = train_test_split(
        X, y, test_size=0.20, stratify=y, random_state=42,
    )

    # ── XGBoost ───────────────────────────────────────────────────────────────
    pos   = int(y_train.sum())
    neg   = int(len(y_train) - pos)
    spw   = round(neg / pos, 4) if pos > 0 else 1.0

    params = {**XGBOOST_PARAMS, 'scale_pos_weight': spw}

    model = xgb.XGBClassifier(**params)
    model.fit(X_train, y_train, eval_set=[(X_test, y_test)], verbose=False)

    # ── Metrics ───────────────────────────────────────────────────────────────
    y_pred  = model.predict(X_test)
    y_proba = model.predict_proba(X_test)[:, 1]

    metrics = {
        'accuracy':  round(float(accuracy_score(y_test, y_pred)), 4),
        'precision': round(float(precision_score(y_test, y_pred, zero_division=0)), 4),
        'recall':    round(float(recall_score(y_test, y_pred, zero_division=0)), 4),
        'f1_score':  round(float(f1_score(y_test, y_pred, zero_division=0)), 4),
        'auc_roc':   round(float(roc_auc_score(y_test, y_proba)), 4),
    }
    logger.info(f"Training metrics: {metrics}")

    # ── Feature importance ────────────────────────────────────────────────────
    importance = {
        name: round(float(score), 6)
        for name, score in zip(FEATURE_NAMES, model.feature_importances_)
    }

    # ── Save model artifacts ──────────────────────────────────────────────────
    version = f"v{datetime.now().strftime('%Y%m%d')}"

    os.makedirs(os.path.dirname(MODEL_PATH), exist_ok=True)

    with open(MODEL_PATH,    'wb') as f:
        pickle.dump(model, f)
    with open(FEATURES_PATH, 'wb') as f:
        pickle.dump(FEATURE_NAMES, f)

    metrics_path = os.path.join(os.path.dirname(MODEL_PATH), 'metrics.json')
    with open(metrics_path, 'w') as f:
        json.dump({'version': version, 'metrics': metrics, 'feature_importance': importance}, f, indent=2)

    logger.info(f"Model saved — version {version}, AUC-ROC {metrics['auc_roc']}")

    return {
        'model_version':      version,
        'data_records_used':  total_records,
        'real_records_used':  real_count,
        'hyperparameters':    params,
        'metrics':            metrics,
        'feature_importance': importance,
    }
