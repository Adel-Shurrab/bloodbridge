"""
Tests for ai_service/training/train.py

Run from ai_service/ directory:
    venv/Scripts/python.exe -m pytest tests/test_training.py -v
"""

import os
import sys
import pickle
import tempfile

import numpy as np
import pandas as pd
import pytest

# Make the ai_service root importable
sys.path.insert(0, os.path.join(os.path.dirname(__file__), '..'))

from training.train import (
    FEATURE_NAMES,
    MIN_REAL_RECORDS,
    XGBOOST_PARAMS,
    _generate_synthetic,
    _engineer_features,
)


# =============================================================================
# FEATURE_NAMES contract — must stay in sync with api/routes.py
# =============================================================================

def test_feature_names_count():
    assert len(FEATURE_NAMES) == 13


def test_feature_names_match_pkl():
    """Retrained model must use same features as the existing model on disk."""
    features_path = os.path.join(os.path.dirname(__file__), '..', 'models', 'feature_names.pkl')
    if not os.path.exists(features_path):
        pytest.skip("feature_names.pkl not on disk — run initial training first")

    with open(features_path, 'rb') as f:
        saved = pickle.load(f)

    assert FEATURE_NAMES == saved, (
        "FEATURE_NAMES in train.py does not match the saved feature_names.pkl. "
        "This will break the /api/score endpoint after retraining."
    )


def test_feature_names_contains_required_fields():
    required = {
        'total_responses', 'days_since_last', 'total_donations',
        'age', 'gender', 'blood_type',
        'urgency_level', 'distance_km', 'hour_of_day', 'day_of_week',
        'acceptance_rate', 'recency_score', 'loyalty_score',
    }
    assert required == set(FEATURE_NAMES)


# =============================================================================
# Synthetic data generation
# =============================================================================

def test_synthetic_shape():
    df = _generate_synthetic(100)
    assert len(df) == 100
    assert set(FEATURE_NAMES).issubset(df.columns)
    assert 'label' in df.columns


def test_synthetic_no_nulls():
    df = _generate_synthetic(200)
    assert df[FEATURE_NAMES + ['label']].isnull().sum().sum() == 0


def test_synthetic_label_distribution():
    """Label should be roughly 40–55% positive (acceptance rate)."""
    df = _generate_synthetic(1000, seed=42)
    rate = df['label'].mean()
    assert 0.35 <= rate <= 0.60, f"Unexpected acceptance rate: {rate:.2f}"


def test_synthetic_acceptance_rate_range():
    df = _generate_synthetic(500)
    assert df['acceptance_rate'].between(0, 1).all()


def test_synthetic_recency_score_range():
    df = _generate_synthetic(500)
    assert df['recency_score'].between(0, 1).all()


def test_synthetic_loyalty_score_range():
    df = _generate_synthetic(500)
    assert df['loyalty_score'].between(0, 1).all()


def test_synthetic_blood_type_range():
    df = _generate_synthetic(500)
    assert df['blood_type'].between(1, 8).all()


def test_synthetic_urgency_values():
    df = _generate_synthetic(500)
    assert set(df['urgency_level'].unique()).issubset({1, 2})


def test_synthetic_is_reproducible():
    df1 = _generate_synthetic(50, seed=99)
    df2 = _generate_synthetic(50, seed=99)
    pd.testing.assert_frame_equal(df1, df2)


def test_synthetic_different_seeds_differ():
    df1 = _generate_synthetic(100, seed=1)
    df2 = _generate_synthetic(100, seed=2)
    assert not df1['label'].equals(df2['label'])


# =============================================================================
# Feature engineering
# =============================================================================

def test_engineer_features_derives_acceptance_rate():
    df = pd.DataFrame({
        'total_responses': [4],
        'days_since_last': [30],
        'total_donations': [2],
        'age': [28],
        'gender': [0],
        'blood_type': [1],
        'accepted_count': [2],
        'no_show_count': [1],
        'urgency_level': [1],
        'distance_km': [5.0],
        'hour_of_day': [10],
        'day_of_week': [2],
    })
    out = _engineer_features(df)
    # acceptance_rate = accepted / (total_clipped + no_show) = 2 / (4+1) = 0.4
    assert abs(out['acceptance_rate'].iloc[0] - 0.4) < 1e-6


def test_engineer_features_recency_score_never_donated():
    """days_since_last = 999 should produce a near-zero recency score."""
    df = pd.DataFrame({
        'total_responses': [0],
        'days_since_last': [999],
        'total_donations': [0],
        'age': [25],
        'gender': [0],
        'blood_type': [1],
        'accepted_count': [0],
        'no_show_count': [0],
        'urgency_level': [1],
        'distance_km': [10.0],
        'hour_of_day': [12],
        'day_of_week': [0],
    })
    out = _engineer_features(df)
    assert out['recency_score'].iloc[0] < 0.01


def test_engineer_features_loyalty_score_clamped():
    df = pd.DataFrame({
        'total_responses': [20],
        'days_since_last': [10],
        'total_donations': [15],   # above 10 — should clamp to 1.0
        'age': [35],
        'gender': [0],
        'blood_type': [3],
        'accepted_count': [15],
        'no_show_count': [0],
        'urgency_level': [2],
        'distance_km': [3.0],
        'hour_of_day': [9],
        'day_of_week': [1],
    })
    out = _engineer_features(df)
    assert out['loyalty_score'].iloc[0] == 1.0


def test_engineer_features_handles_nulls():
    df = pd.DataFrame({
        'total_responses': [None],
        'days_since_last': [None],
        'total_donations': [None],
        'age': [None],
        'gender': [None],
        'blood_type': [None],
        'accepted_count': [None],
        'no_show_count': [None],
        'urgency_level': [1],
        'distance_km': [10.0],
        'hour_of_day': [12],
        'day_of_week': [0],
    })
    out = _engineer_features(df)
    # Should not raise — nulls must be filled
    assert out[['acceptance_rate', 'recency_score', 'loyalty_score']].isnull().sum().sum() == 0


# =============================================================================
# Constants
# =============================================================================

def test_min_real_records_is_positive():
    assert MIN_REAL_RECORDS > 0


def test_xgboost_params_has_required_keys():
    required = {'max_depth', 'learning_rate', 'n_estimators', 'subsample', 'colsample_bytree'}
    assert required.issubset(XGBOOST_PARAMS.keys())


def test_xgboost_max_depth_reasonable():
    assert 2 <= XGBOOST_PARAMS['max_depth'] <= 8


def test_xgboost_learning_rate_reasonable():
    assert 0.01 <= XGBOOST_PARAMS['learning_rate'] <= 0.5
