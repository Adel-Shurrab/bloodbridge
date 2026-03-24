# Predictive Donor Scoring System - Plan Ratification ✅

**Date**: March 18, 2026  
**Status**: APPROVED & RATIFIED (نعتمد)  
**Owner**: Development Team  

---

## Approval Summary

The **Predictive Donor Scoring System** with integrated **Epsilon-Greedy Exploration** has been reviewed, refined, and approved for implementation with the following constraints and agreements:

### APPROVED Architecture
- ✅ Machine Learning Model: XGBoost binary classification
- ✅ Deployment: Python FastAPI microservice (no external APIs)
- ✅ Prediction Timing: Hybrid (cache + real-time refresh)
- ✅ Cold-Start Strategy: Boost new donors to 0.7
- ✅ Feature Set: Donor-only behavioral metrics (MVP)
- ✅ Retraining: Weekly (Sundays 2am)

### APPROVED Enhancements
- ✅ **Epsilon-Greedy Exploration**: Prevent filter bubble, diverse donor sampling
- ✅ **Epsilon Decay**: Time-based maturation (20% → 5% over 13 weeks)
- ✅ **Urgency-Aware Strategy**: CRITICAL (3% fixed) vs NORMAL (decay)

### RATIFIED Constraints (نعتمد)
- ✅ **NO new urgency enums**: Use existing CRITICAL/NORMAL only
  - ❌ REMOVED: EXTREMELY_CRITICAL (overshoot logic)
  - ❌ REMOVED: PLANNED (exploration-focused broadcasts)
  - ✅ RETAINED: CRITICAL (< 24 hours), NORMAL (< 7 days)

- ✅ **NO new Phase 1 migrations**: Phase 1 schema is FINAL
  - 3 tables complete: `donor_predictive_scores`, `donor_behavioral_metrics`, `model_training_logs`
  - All migrations executed successfully ✅

- ✅ **Epsilon-greedy features in Phase 3 only**: Not Phase 1
  - `was_exploration` field added in Phase 3 migration (request_responses table)
  - No changes to Phase 1 schema files

---

## Implementation Timeline (REVISED)

| Phase | Duration | Status | Notes |
|-------|----------|--------|-------|
| **1. Schema + Models** | **2 days** | **✅ COMPLETE** | Migrations executed; models instantiated |
| **2. Python ML Service** | **3 days** | ⏳ PENDING | Data pipelines, feature engineering, XGBoost training |
| **3. Laravel Integration** | **7.25 days** | ⏳ PENDING | Service injection + epsilon-greedy integration |
| **4. Validation & Testing** | **1.5 days** | ⏳ PENDING | A/B setup, decay verification, monitoring tests |
| **5. Monitoring** | **Ongoing** | ⏳ PENDING | Weekly decay tracking, exploration analytics |
| **TOTAL** | **~14 days** | **2-3 sprints** | MVP with epsilon-greedy fully integrated |

---

## Phase 1: Verified Complete ✅

### Tables Created
```
✅ donor_predictive_scores (179.44ms)
✅ donor_behavioral_metrics (227.81ms)
✅ model_training_logs (59.32ms)
```

### Models Created
```
✅ app/Models/DonorPredictiveScore
✅ app/Models/DonorBehavioralMetric
✅ app/Models/ModelTrainingLog
```

### Key Features in Phase 1
- ✅ `DonorPredictiveScore`: Caches acceptance_probability (refreshed weekly)
- ✅ `DonorBehavioralMetric`: Caches 15+ behavioral features
- ✅ `ModelTrainingLog`: Audit trail with metrics, hyperparameters, feature_importance
- ✅ Modern Laravel syntax: `foreignIdFor(Model::class)` throughout

---

## Phase 2: Python ML Service (Ready to Start)

### Scope
1. Data pipeline: Extract donor + response history
2. Feature engineering: 15-20 features from behavioral metrics
3. Model training: XGBoost with weekly Sunday retraining
4. FastAPI endpoints: 
   - `POST /predict` - Single donor score
   - `POST /predict-batch` - Bulk scoring
   - `POST /retrain` - Trigger weekly retraining

### Timeline
- Days 1-2: Data pipeline + feature engineering
- Day 3: Model training + API setup + error handling

---

## Phase 3: Laravel Integration + Epsilon-Greedy (Updated)

### Part A: Service Integration (2 days)
1. Create `DonorScoringService` (FastAPI orchestrator)
2. Add Redis caching (24h TTL for predictions)
3. Integrate with `BloodRequestBroadcastService`
4. Error handling + fallback to geospatial logic

### Part B: Epsilon-Greedy (5.25 days)
1. **Implement Epsilon Decay** (1 day)
   - `getDecayedEpsilon()`: Exponential curve (20% → 5%)
   - `getModelAgeWeeks()`: Weeks since first training
   - `shouldReduceEpsilon()`: Automatic maturity detection

2. **Implement Urgency-Based Epsilon** (0.5 days)
   - `getEpsilonByUrgency()`: CRITICAL (3%) vs NORMAL (decay)

3. **Implement Exploration Strategy** (1.5 days)
   - `filterDonorsByThresholdWithExploration()`: Multi-tier sampling
   - Tier 1 (0.8+): Exploit primarily
   - Tier 2-4 (0.2-0.8): Weighted random exploration
   - Tier 5 (<0.2): Low probability sampling

4. **Tracking & Metrics** (0.75 days)
   - Add `was_exploration` boolean to request_responses (Phase 3 migration)
   - Log exploration decisions for analysis

5. **Dashboard Widget** (0.75 days)
   - Decay progress indicator (Week 1-2: green, Week 3-4: yellow, Week 5+: blue)
   - Exploration metrics (% explored, acceptance by tier)

6. **Testing & Docs** (0.75 days)
   - Unit tests: Epsilon decay, tier selection
   - Integration tests: Urgency switches, decay progression
   - Production runbook: Weekly decay checklist

---

## Phase 4 & 5: Validation & Monitoring

### Validation (1.5 days)
- A/B test: ML-scored vs geospatial baseline
- Decay verification: Monitor epsilon reduction over 13 weeks
- Exploration ROI: % of explorers becoming top-tier

### Monitoring (Ongoing)
- Daily SQL checks: Exploration acceptance rate vs exploit rate
- Weekly: Review decay progress, model accuracy
- Monthly: Adjust epsilon decay if needed

---

## Key Documents

| Document | Status | Purpose |
|----------|--------|---------|
| [PREDICTIVE_DONOR_SCORING_SYSTEM.md](PREDICTIVE_DONOR_SCORING_SYSTEM.md) | ✅ FINAL | Architecture, schema, full implementation guide |
| [EPSILON_GREEDY_EXPLORATION_STRATEGY.md](EPSILON_GREEDY_EXPLORATION_STRATEGY.md) | ✅ REVISED | Exploration strategy, decay logic, monitoring |

---

## Success Criteria

### Primary Metrics
- ✅ Acceptance rate: 50-60% (vs 35% baseline) = **+40-70% improvement**
- ✅ Donor fatigue: < 5 notifications/month for 95%+ of donors
- ✅ Model accuracy: AUC-ROC > 0.72

### Epsilon-Greedy Metrics
- ✅ Decay progression: Epsilon from 20% → 5% over 13 weeks
- ✅ Exploration ROI: 30%+ of explorers become top-tier
- ✅ Diversity: 20-30% of explorers from tiers 2-4

---

## Risk Mitigation

| Risk | Mitigation |
|------|-----------|
| Model predictions are poor | Fallback to geospatial logic; retrain with adjusted hyperparameters |
| Exploration reduces acceptance rate | Monitor daily; adjust epsilon down if needed |
| Decay breaks in week 8 | Weekly reviews; manual decay adjustment if stability metric drops |
| Epsilon never stabilizes at 5% | Switch to Thompson Sampling (Phase 4 enhancement) |

---

## Next Steps

1. ✅ **TODAY**: Finalize Phase 1 database schema (COMPLETE)
2. **WEEK 1**: Begin Phase 2 (Python ML service development)
3. **WEEK 2**: Start Phase 3 (Laravel integration + epsilon-greedy)
4. **WEEK 3**: Finish Phase 4 & 5 (validation + monitoring setup)
5. **WEEK 4 ONWARDS**: Production monitoring + iterative tuning

---

## Sign-Off

**Plan Status**: ✅ **APPROVED & RATIFIED (نعتمد)**

This implementation plan has been reviewed against constraints and approved for execution. Phase 1 schema is complete and verified. Phases 2-5 are ready to begin on schedule.

---

**Date Approved**: March 18, 2026  
**Last Updated**: March 18, 2026  
**Approval Authority**: Development Team  
