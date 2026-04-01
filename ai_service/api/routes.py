import pickle
import psutil
import logging
import numpy as np
import pandas as pd

from fastapi import APIRouter, HTTPException, Request
from pydantic import BaseModel, field_validator
from slowapi import Limiter
from slowapi.util import get_remote_address
from sqlalchemy import create_engine, text
from typing import Dict, List, Optional
from datetime import datetime

from config import (
    MODEL_PATH,
    FEATURES_PATH,
    NEUTRAL_SCORE,
    MIN_HISTORY_FOR_MODEL,
    DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD
)

logger = logging.getLogger(__name__)
router = APIRouter(prefix='/api', tags=['scoring'])
limiter = Limiter(key_func=get_remote_address)

# Database engine
engine = create_engine(
    f'mysql+pymysql://{DB_USER}:{DB_PASSWORD}@{DB_HOST}:{DB_PORT}/{DB_NAME}',
    pool_pre_ping=True,
)


# =========================================================================
# Schemas — define what goes in and what comes out
# =========================================================================

class ScoreRequest(BaseModel):
    donor_ids: List[int]
    urgency: Optional[str] = 'normal'
    distances: Optional[Dict[int, float]] = None

    @field_validator('donor_ids')
    @classmethod
    def validate_donor_ids(cls, v):
        if len(v) == 0:
            raise ValueError('donor_ids must not be empty')
        if len(v) > 500:
            raise ValueError('Maximum 500 donors per request')
        if len(set(v)) != len(v):
            raise ValueError('donor_ids must not contain duplicates')
        return v


class DonorScore(BaseModel):
    score:         float
    is_cold_start: bool


class ScoreResponse(BaseModel):
    scores: Dict[int, DonorScore]


class HealthResponse(BaseModel):
    status:           str
    model_loaded:     bool
    model_version:    Optional[str]
    last_trained:     Optional[str]
    db_connected:     bool
    memory_usage_pct: float
    details:          Dict


# =========================================================================
# Helpers
# =========================================================================

def load_model():
    """Load model from disk. Returns None if not found."""
    try:
        with open(MODEL_PATH, 'rb') as f:
            return pickle.load(f)
    except FileNotFoundError:
        return None


def load_feature_names():
    """Load feature names from disk. Returns empty list if not found."""
    try:
        with open(FEATURES_PATH, 'rb') as f:
            return pickle.load(f)
    except FileNotFoundError:
        return []


def get_donor_features(donor_ids: list, urgency: str = 'normal', distances: dict = None) -> pd.DataFrame:
    """
    Fetch donor features from database in a single query.
    Same features as our Jupyter notebook training data.
    """
    placeholders = ', '.join([f':id_{i}' for i in range(len(donor_ids))])
    params = {f'id_{i}': int(did) for i, did in enumerate(donor_ids)}

    query = text(f"""
        SELECT
            d.id                                                            AS donor_id,
            COUNT(rr.id)                                                    AS total_responses,
            COALESCE(DATEDIFF(NOW(), MAX(rr.responded_at)), 999)            AS days_since_last,
            COALESCE(dhp.total_donations, 0)                                AS total_donations,
            TIMESTAMPDIFF(YEAR, d.birth_date, NOW())                        AS age,
            CASE d.gender WHEN 'male' THEN 0 ELSE 1 END                    AS gender,
            COALESCE(dhp.blood_type, 0)                                     AS blood_type,
            COUNT(CASE WHEN rr.status IN (1,3) THEN 1 END)                  AS accepted_count,
            COUNT(CASE WHEN rr.status = 5     THEN 1 END)                   AS no_show_count
        FROM donors d
        LEFT JOIN request_responses rr
               ON d.id      = rr.donor_id
              AND rr.status IN (1,2,3,4,5,6,7)
              AND rr.responded_at IS NOT NULL
        LEFT JOIN donor_health_profiles dhp ON d.id = dhp.donor_id
        WHERE d.id IN ({placeholders})
        AND  (d.deleted_at IS NULL)
        GROUP BY d.id, dhp.total_donations, dhp.blood_type,
                 d.birth_date, d.gender
    """)

    with engine.connect() as conn:
        df = pd.read_sql(query, conn, params=params)

    df = df.fillna({
        'total_responses': 0,
        'days_since_last': 999,
        'total_donations': 0,
        'age':             30,
        'gender':          0,
        'blood_type':      0,
        'accepted_count':  0,
        'no_show_count':   0,
    })

    total = df['total_responses'].clip(lower=1)

    # acceptance_rate — with NO_SHOW penalty (same as Laravel)
    no_show_penalty = df['no_show_count']
    adjusted_total = total + no_show_penalty
    df['acceptance_rate'] = df['accepted_count'] / adjusted_total

    # recency_score — exponential decay
    df['recency_score'] = np.exp(-df['days_since_last'] / 60)

    # loyalty_score
    df['loyalty_score'] = (df['total_donations'] / 10).clip(0, 1)

    # urgency_level: NORMAL=1, CRITICAL=2 (matches Laravel UrgencyLevel enum)
    df['urgency_level'] = 2 if urgency == 'critical' else 1

    # distance_km: use per-donor real distance if provided, fall back to 10.0
    if distances:
        df['distance_km'] = df['donor_id'].map(distances).fillna(10.0)
    else:
        df['distance_km'] = 10.0

    df['hour_of_day'] = datetime.now().hour
    df['day_of_week'] = datetime.now().weekday()

    return df


# =========================================================================
# Endpoints
# =========================================================================

@router.get('/health', response_model=HealthResponse)
async def health():
    """
    Structured health check.
    Laravel calls this before each broadcast via CircuitBreaker.
    """
    details = {}
    model_loaded = False
    model_version = None
    last_trained = None
    db_connected = False

    # Check model
    try:
        model = load_model()
        model_loaded = model is not None
        details['model'] = 'loaded' if model_loaded else 'not found — run training first'
    except Exception as e:
        details['model'] = f'error: {str(e)}'

    # Check database + last training log
    try:
        with engine.connect() as conn:
            db_connected = True
            row = conn.execute(text(
                "SELECT model_version, training_date "
                "FROM model_training_logs "
                "ORDER BY training_date DESC LIMIT 1"
            )).fetchone()
            if row:
                model_version = row.model_version
                last_trained = str(row.training_date)
        details['database'] = 'connected'
    except Exception as e:
        details['database'] = f'error: {str(e)}'

    # Check memory
    memory_pct = psutil.virtual_memory().percent
    details['memory_pct'] = memory_pct

    status = (
        'healthy' if db_connected and model_loaded else
        'degraded' if db_connected or model_loaded else
        'unhealthy'
    )

    return HealthResponse(
        status=status,
        model_loaded=model_loaded,
        model_version=model_version,
        last_trained=last_trained,
        db_connected=db_connected,
        memory_usage_pct=memory_pct,
        details=details,
    )


@router.post('/score', response_model=ScoreResponse)
@limiter.limit("100/minute")
async def score_donors(request: Request, body: ScoreRequest):
    """
    Score donors for acceptance probability.

    Returns DonorScore per donor_id:
      score:         float 0.0-1.0
      is_cold_start: bool — true = insufficient history
                            Laravel routes to exploration bucket
    """
    model = load_model()
    feature_names = load_feature_names()

    # No model yet — mark all as cold start
    if model is None or not feature_names:
        return ScoreResponse(scores={
            did: DonorScore(score=NEUTRAL_SCORE, is_cold_start=True)
            for did in body.donor_ids
        })

    # Fetch features from DB
    features_df = get_donor_features(body.donor_ids, body.urgency, body.distances)

    # Get response counts for cold-start detection
    placeholders = ', '.join([f':id_{i}' for i in range(len(body.donor_ids))])
    params = {f'id_{i}': did for i, did in enumerate(body.donor_ids)}

    with engine.connect() as conn:
        count_rows = conn.execute(text(f"""
            SELECT donor_id, COUNT(*) AS response_count
            FROM request_responses
            WHERE donor_id IN ({placeholders})
            AND   status   IN (1, 2, 3, 4, 5, 6, 7)
            GROUP BY donor_id
        """), params).fetchall()

    response_counts = {row.donor_id: row.response_count for row in count_rows}

    scores = {}

    for _, row in features_df.iterrows():
        donor_id = int(row['donor_id'])
        history_count = response_counts.get(donor_id, 0)
        is_cold_start = history_count < MIN_HISTORY_FOR_MODEL

        if is_cold_start:
            scores[donor_id] = DonorScore(
                score=NEUTRAL_SCORE,
                is_cold_start=True
            )
        else:
            # Score using XGBoost model
            X = row[feature_names].values.reshape(1, -1)
            prob = float(model.predict_proba(X)[0][1])
            scores[donor_id] = DonorScore(
                score=round(prob, 4),
                is_cold_start=False
            )

    cold_count = sum(1 for s in scores.values() if s.is_cold_start)
    logger.info(f"Scored {len(scores)} donors. Cold-start: {cold_count}")

    return ScoreResponse(scores=scores)


@router.post('/retrain')
@limiter.limit("5/hour")
async def retrain(request: Request):
    """Manually trigger model retraining."""
    logger.info("Retraining triggered")
    return {
        'status':  'acknowledged',
        'message': 'Retraining request received',
    }
