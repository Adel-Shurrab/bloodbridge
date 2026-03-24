# Epsilon-Greedy Exploration Strategy for Donor Broadcasting

**Document Version**: 1.0  
**Date**: March 18, 2026  
**Status**: Design Document (Integrated with Phase 3)

---

## Executive Summary

To prevent the **Filter Bubble effect** in predictive donor scoring, implement a probabilistic exploration strategy that intentionally notifies a small percentage of lower-scored donors alongside the high-probability acceptors. This ensures:

- ✅ Diverse training data for model retraining
- ✅ Fair opportunities for all donor tiers
- ✅ Early detection of behavior changes
- ✅ Reduced model convergence bias

---

## The Problem: Filter Bubble

### Current Risk (Without Exploration)

```
Week 1:  Model predicts: Top 20 donors (scores 0.8-1.0)
         ↓
         Notify top 20 → Get responses only from them
         ↓
Week 2:  Retrain on data from top 20 only
         ↓
         Model's top tier scores increase further (0.9→0.95)
         ↓
Week 3:  Middle tier (scores 0.5-0.7) NEVER notified
         ↓
Week 4+: Model frozen on narrow subset; misses recovered donors
```

### Consequences
- **Lost Donors**: Active donors in 0.4-0.7 range become invisible
- **Model Degradation**: Training on biased subset only
- **Unfairness**: Some donors permanently excluded
- **Opportunity Cost**: Miss re-engaged donors with improved health

---

## Solution: Epsilon-Greedy Strategy

### Core Concept

In every broadcast, allocate donor notifications into two buckets:

| Bucket | Strategy | % Allocation | Purpose |
|--------|----------|-------------|---------|
| **Exploitation** | Notify highest-scored donors | (1 - ε)% | Optimize acceptance rate |
| **Exploration** | Randomly notify lower-scored donors | ε% | Discover new patterns |

**Where ε (epsilon) = exploration rate** (typically 5-10%)

### Example: 50 Eligible Donors, 10 Slots to Fill

```
Epsilon = 0.07 (7%)

Exploitation tier (0.8-1.0):      30 donors
→ Notify: 30 × 93% = ~28 donors max
→ Select: Top 9 by score

Exploration tier (0.3-0.8):       20 donors
→ Notify: 20 × 7% = ~1-2 donors random
→ Select: 1 random from mid/low tier

Final result: 9-10 donors notified (balanced exploit/explore)
```

---

## Implementation Strategy

### Phase 1: Adaptive Epsilon by Urgency Level

Work with existing urgency levels (CRITICAL and NORMAL):

```php
$epsilon = match ($bloodRequest->urgency_level->value) {
    UrgencyLevel::CRITICAL->value => 0.03,   // 3% - Critical/emergency: minimal exploration
    UrgencyLevel::NORMAL->value   => $this->getDecayedEpsilon(),  // Decayed: 20% → 5%
};
```

**Rationale**:
- **Critical** (urgent/emergency, < 24 hours): Minimize exploration (3%); prioritize proven acceptors
- **Normal** (routine/standard requests): Dynamic epsilon that decays as model matures (20% → 5%)

---

### Phase 1.5: Epsilon Decay (Time-Based Maturation)

Instead of constant epsilon, decay it as model confidence increases:

**Decay Schedule**:
```
Week 0-2:   ε = 20%   (Bootstrap: Build diverse training data fast)
Week 3-4:   ε = 15%   (Growth: Model sees patterns, but still learning)
Week 5-8:   ε = 10%   (Stability: Model capturing trends reliably)
Week 9-12:  ε = 7%    (Maturity: Model well-calibrated)
Week 13+:   ε = 5%    (Steady-State: Minimal exploration, max exploitation)
```

**Implementation**:
```php
private function getDecayedEpsilon(): float
{
    // Get model age since first training
    $firstTraining = ModelTrainingLog::oldest('training_date')->first();
    $modelAgeWeeks = $firstTraining ? now()->diffInWeeks($firstTraining->training_date) : 0;
    
    // Decay curve: max(5%, 20% * e^(-0.25 * weeks))
    $epsilon = 0.20 * exp(-0.25 * $modelAgeWeeks);
    return max(0.05, $epsilon);
}

// Alternative: Sigmoid decay (smoother transition)
private function getDecayedEpsilonSigmoid(): float
{
    $weeks = $this->getModelAgeWeeks();
    // Sigmoid: 20% at week 0 → 5% at week 12
    $epsilon = 0.075 + (0.125 / (1 + exp(0.5 * ($weeks - 6))));
    return $epsilon;
}
```

**Maturity Detection** (Automatic decay trigger):
```php
private function shouldReduceEpsilon(): bool
{
    $latestLog = ModelTrainingLog::latest('training_date')->first();
    
    if (!$latestLog) return false;
    
    $metrics = $latestLog->metrics;
    
    // Reduce epsilon if:
    return $metrics['auc_roc'] > 0.75          // Model is accurate
        && $metrics['stability'] > 0.9         // Predictions haven't drifted
        && $this->getExplorationROI() > 0.25;  // Only 25%+ explorers become high-scorers
}

private function getExplorationROI(): float
{
    // % of accepted explorers who become top-tier in next model version
    $accepted_explorers = RequestResponse::where('was_exploration', true)
        ->where('status', RequestResponseStatus::ACCEPTED->value)
        ->where('created_at', '>=', now()->subWeek())
        ->pluck('donor_id');
    
    if ($accepted_explorers->isEmpty()) return 0;
    
    $became_high_scorers = DonorPredictiveScore::whereIn('donor_id', $accepted_explorers)
        ->where('acceptance_probability', '>', 0.7)
        ->count();
    
    return $became_high_scorers / $accepted_explorers->count();
}
```

**Why Decay Works**:
- **Early phase (high exploration)**: You need diverse data to train the model; don't know which donors are good yet
- **Middle phase (moderate exploration)**: Model is learning patterns; still discovering outliers
- **Late phase (low exploration)**: Model is accurate enough to trust rankings; exploration noise hurts acceptance rate
- **Steady-state (minimal exploration)**: 5-7% keeps data fresh while optimizing for current performance

### Phase 2: Multi-Tier Donor Allocation

Instead of binary top/bottom split, use 5 tiers with tiered exploration:

```php
private function filterDonorsByThresholdWithExploration(
    Collection $donors, 
    BloodRequest $bloodRequest
): Collection {
    // Determine epsilon
    $epsilon = $this->getEpsilonByUrgency($bloodRequest->urgency_level);
    
    // Segment donors by score
    $tier1 = $donors->filter(fn($d) => $d->score >= 0.8);    // Elite
    $tier2 = $donors->filter(fn($d) => $d->score >= 0.6 && $d->score < 0.8);  // Good
    $tier3 = $donors->filter(fn($d) => $d->score >= 0.4 && $d->score < 0.6);  // Fair
    $tier4 = $donors->filter(fn($d) => $d->score >= 0.2 && $d->score < 0.4);  // Poor
    $tier5 = $donors->filter(fn($d) => $d->score < 0.2);     // Very Poor
    
    // Exploitation: Fill slots with top tiers
    $exploit_count = ceil($donors->count() * (1 - $epsilon));
    $exploit = $tier1->take(ceil($exploit_count * 0.6))
        ->concat($tier2->take(ceil($exploit_count * 0.3)))
        ->concat($tier3->take(ceil($exploit_count * 0.1)));
    
    // Exploration: Sample from lower tiers probabilistically
    $explore = collect();
    $remaining_slots = $this->desired_notification_count - $exploit->count();
    
    if ($remaining_slots > 0) {
        // Weighted random sampling: tier 2 > tier 3 > tier 4
        $explore = $this->weightedRandomSample(
            $tier2->concat($tier3)->concat($tier4),
            $remaining_slots,
            [0.5, 0.3, 0.2]  // Probability distribution
        );
    }
    
    // Combine & mark exploration
    $result = $exploit->concat($explore);
    $explore->each(fn($d) => $d->exploration = true);
    
    return $result;
}
```

---

## Database Schema Extension

Add tracking field to `request_responses` table:

```php
// Migration: 2026_03_18_add_exploration_tracking_to_request_responses.php
Schema::table('request_responses', function (Blueprint $table) {
    $table->boolean('was_exploration')->default(false)
        ->after('status')
        ->comment('True if donor was notified via exploration strategy (not top-scored)');
    
    $table->index(['was_exploration', 'status']);
});
```

### Tracking Fields
- `was_exploration` (boolean): Whether this notification was part of exploration bucket
- `explored_tier` (optional, string): Which tier donor was in when notified
  - Values: 0.8+, 0.6-0.8, 0.4-0.6, 0.2-0.4, <0.2

---

## Metrics & Analytics

### Dashboard Queries

```sql
-- Compare acceptance rates by strategy
SELECT 
    was_exploration,
    status,
    COUNT(*) as total,
    (COUNT(CASE WHEN status = 1 THEN 1 END) / COUNT(*)) as acceptance_rate
FROM request_responses
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY was_exploration, status;

-- Expected output:
-- | was_exploration | acceptance_rate |
-- |        0        |     0.42        |  Exploitation (should be high)
-- |        1        |     0.15-0.25   |  Exploration (lower, but validates model)
```

### Key Metrics to Track

1. **Exploitation Acceptance Rate** (E_accept)
   - % of top-tier donors who accept
   - Target: 40-60%
   - If < 30%: Model may be poorly calibrated

2. **Exploration Acceptance Rate** (E_explore)
   - % of random-tier donors who accept
   - Target: 10-25%
   - If > E_accept: Model is missing high-value donors!

3. **Exploration ROI**
   - % of accepted explorers who become high-scorers in next week
   - Target: > 30% (they improve their pattern)

4. **Model Recall** (per tier)
   - Among all acceptors in week N, how many were in top tier week N-1?
   - Low recall = model missing good candidates

### Filament Admin Dashboard Widget

```php
namespace App\Filament\Admin\Widgets;

class ExplorationMetricsWidget extends StatisticsOverviewWidget
{
    protected function getStats(): array
    {
        $exploration_rate = RequestResponse::where('was_exploration', true)->count() 
            / RequestResponse::count();
        
        $exploit_accept = RequestResponse::where('was_exploration', false)
            ->where('status', RequestResponseStatus::ACCEPTED->value)
            ->count() / RequestResponse::where('was_exploration', false)->count();
        
        $explore_accept = RequestResponse::where('was_exploration', true)
            ->where('status', RequestResponseStatus::ACCEPTED->value)
            ->count() / RequestResponse::where('was_exploration', true)->count();
        
        return [
            Stat::make('Exploration Rate', number_format($exploration_rate * 100, 1) . '%')
                ->description('% of notifications via exploration'),
            
            Stat::make('Exploit Acceptance', number_format($exploit_accept * 100, 1) . '%')
                ->description('Top-tier donor acceptance rate'),
            
            Stat::make('Explore Acceptance', number_format($explore_accept * 100, 1) . '%')
                ->description('Random-tier donor acceptance rate'),
            
            Stat::make('Explore/Exploit Ratio', number_format($explore_accept / $exploit_accept, 2))
                ->description('If > 0.5: Model may be undervaluing mid-tier donors'),
        ];
    }
}
```

---

## Advanced: Context-Aware Epsilon

### Blood Type Rarity Effect

Adjust epsilon based on blood type scarcity:

```php
// If blood type is rarer, be more aggressive with exploration
$blood_type_scarcity = $this->getBloodTypeScarcity($bloodRequest->blood_type);
// 0 = abundant (O+), 1 = critical (AB-)

$epsilon = 0.07 * (1 + $blood_type_scarcity);
// Scarcity 0.0 → epsilon = 7%
// Scarcity 1.0 → epsilon = 14%
```

### Seasonal Effects

Adjust epsilon based on time-of-year donation patterns:

```php
$season = $this->getCurrentSeason();  // Winter, Spring, Summer, Fall
$seasonal_epsilon = [
    'winter'  => 0.05,  // Winter = higher donor engagement, explore less
    'spring'  => 0.07,
    'summer'  => 0.10,  // Summer = vacation season, lower engagement, explore more
    'fall'    => 0.08,
];
```

---

## A/B Testing (Optional Phase)

To validate epsilon-greedy effectiveness:

```php
// Filament setting: enable/disable experiment
$control_group_enabled = Setting::get('ab_test_epsilon_greedy');

if ($control_group_enabled && rand(0, 100) < 50) {
    // Control: Exploit-only (old system)
    return $this->filterDonorsByThreshold($donors, $bloodRequest);
} else {
    // Experiment: Epsilon-greedy
    return $this->filterDonorsByThresholdWithExploration($donors, $bloodRequest);
}

// Measure over 2-4 weeks:
// - Control acceptance rate
// - Experiment acceptance rate
// - Donor retention (% who respond again next week)
// - Model drift (accuracy over time)
```

---

## Phase 3: Critical Request Handling

For **CRITICAL** requests (urgent/emergency, < 24 hours), use minimal exploration (3%):

```php
// CRITICAL: Minimal exploration (3%)
if ($bloodRequest->urgency_level === UrgencyLevel::CRITICAL) {
    // Notify mostly top scorers, but 3% random probing for data diversity
    $epsilon = 0.03;
    return $this->filterWithEpsilon($donors, $epsilon, $bloodRequest);
}
```

**Rationale**:
- **Time pressure**: CRITICAL requests need quick confirmations
- **Conservative exploration**: 3% allows learning without sacrificing acceptance rate
- **No new enum levels**: Work within existing CRITICAL/NORMAL structure

---

## Convergence & Tuning

### Epsilon Decay Monitoring

**Weekly Checklist**:
```
Week 1-2:  ε = 20%  (High exploration)
           Metrics: Collect diverse donor responses
           Action: Monitor acceptance rates (may be low due to exploration)
           
Week 3-4:  ε = 15%  (Decay starting)
           Metrics: Check model AUC-ROC (should be > 0.65)
           Action: If AUC < 0.60, revert to ε = 20%
           
Week 5-8:  ε = 10%  (Moderate exploration)
           Metrics: Check Exploration ROI (% who become high-scorers)
           Action: If ROI > 30%, safe to continue decay
           
Week 9-12: ε = 7%   (Conservative exploration)
           Metrics: Check model drift (accuracy drop > 5%?)
           Action: If stable, reduce to ε = 5%
           
Week 13+:  ε = 5%   (Steady-state)
           Metrics: Monitor monthly for drift
           Action: If drift detected, increase ε temporarily
```

### Finding Optimal Epsilon (If Not Using Decay)

If you prefer manual tuning instead of automatic decay:

```
Start: ε = 7% (neutral)

If Explore_accept ≈ Exploit_accept × 0.4:
  → Epsilon is good, keep at 7%
  
If Explore_accept > Exploit_accept:
  → Model is undervaluing, increase ε to 10%
  
If Explore_accept < 0.1:
  → Epsilon is too high, reduce to 3%
  
If acceptance_rate overall drops > 15%:
  → Exploration is too aggressive, reduce ε by 3%
```

### Decision Tree

```
Is Explore_accept > 35% of Exploit_accept?
  ├─ YES: Model may be missing productive donors
  │        Action: Increase epsilon by 2%
  │
  └─ NO: Model is well-calibrated
           Action: Keep current epsilon
           
Does model accuracy drop > 5% after retrain?
  ├─ YES: Training data is diverging
  │        Action: Reduce epsilon (less exploration noise)
  │
  └─ NO: Model is stable
           Action: Continue current settings
```

---

## Implementation Checklist

### Core Features
- [ ] Add `was_exploration` boolean field to `request_responses` table
- [ ] Implement `getEpsilonByUrgency()` helper method (CRITICAL=3%, NORMAL=decay)
- [ ] Implement `getDecayedEpsilon()` for time-based decay
- [ ] Implement `shouldReduceEpsilon()` for automatic maturity detection
- [ ] Implement `weightedRandomSample()` for tier-based exploration

### Dashboard & Monitoring
- [ ] Create `ExplorationMetricsWidget` for Filament dashboard
- [ ] Add decay progress indicator (Week 1-2: green, Week 3-4: yellow, Week 5+: blue)
- [ ] Write SQL queries for exploration analytics

### Testing & Validation
- [ ] Create tests for epsilon logic:
  - [ ] Test exploitation tier selection
  - [ ] Test exploration tier randomization
  - [ ] Test epsilon-by-urgency levels (CRITICAL=3%, NORMAL=decay)
  - [ ] Test tier distribution across 5 tiers
  - [ ] Test decay schedule (exponential/sigmoid)
- [ ] Integration tests:
  - [ ] Verify normal → critical request uses different epsilon
  - [ ] Verify decay reduces epsilon over weeks

### Documentation
- [ ] Update production runbook with decay schedule
- [ ] Document urgency-based epsilon strategy (CRITICAL=3%, NORMAL=decay)
- [ ] Add monitoring checklist for weekly decay reviews
- [ ] Log exploration decisions to audit trail (for debugging)
- [ ] Set up alerts for:
  - [ ] Model AUC drops below 0.60 (halt decay, investigate)
  - [ ] Exploration ROI drops below 20% (increase epsilon)

---

## Risk Mitigation

| Risk | Mitigation |
|------|-----------|
| **Too much exploration** (ε > 15%) | Reduces acceptance rate. Start at 7%, tune down. |
| **Exploration overloads low-scorers** | Use weighted random (tier2 > tier3 > tier4). |
| **Model gets noisy from exploration** | Use class_weight in XGBoost; monitor accuracy. |
| **Ethical concern: "unfair" to notify low scorers** | Counter: Historical data may be stale (life circumstances change). |
| **Exploration never helps (constant low acceptance)** | Evidence that model is accurate; can reduce ε further. |

---

## Related Concepts

### Thompson Sampling (Advanced Alternative)

Instead of fixed epsilon, use Bayesian posterior sampling:

```python
# Each donor has Beta(α, β) posterior of acceptance probability
# Sample from posterior instead of deterministic score

def thompson_sample(donors):
    samples = []
    for donor in donors:
        alpha = donor.accept_count + 1
        beta = donor.reject_count + 1
        sample = np.random.beta(alpha, beta)
        samples.append(sample)
    
    # Sort by sampled probability (not deterministic score)
    return sorted_by_samples(donors, samples)
```

**Advantage**: Automatically balances explore/exploit without epsilon tuning  
**Complexity**: Medium (requires Bayesian framework)  
**Timeline**: Phase 4+ enhancement

### Contextual Bandits (Far Advanced)

Use request context (blood type, urgency, location) to predict optimal exploration per broadcast. Requires RL framework.

---

## Production Runbook

### Monitoring Epsilon-Greedy Daily

```bash
# SQL check (paste into admin dashboard)
SELECT 
    DATE(created_at) as date,
    COUNT(*) as total_responses,
    SUM(CASE WHEN was_exploration = 1 THEN 1 ELSE 0 END) as exploration_count,
    ROUND(SUM(CASE WHEN was_exploration = 1 AND status = 1 THEN 1 ELSE 0 END) 
          / NULLIF(SUM(CASE WHEN was_exploration = 1 THEN 1 ELSE 0 END), 0), 3) as explore_accept_rate,
    ROUND(SUM(CASE WHEN was_exploration = 0 AND status = 1 THEN 1 ELSE 0 END) 
          / NULLIF(SUM(CASE WHEN was_exploration = 0 THEN 1 ELSE 0 END), 0), 3) as exploit_accept_rate
FROM request_responses
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY DATE(created_at)
ORDER BY date DESC;
```

### Alert Conditions

- **ALERT**: `explore_accept_rate > exploit_accept_rate` (model missing donors)
- **ALERT**: `explore_accept_rate < 0.05` and `epsilon > 0.07` (exploration not working)
- **INFO**: `exploration_count < 5% of total` (epsilon too low, may miss drift)

---

## Timeline & Effort

| Task | Duration | FTE | Notes |
|------|----------|-----|-------|
| Add `was_exploration` field to request_responses | 0.5d | 1 | Phase 3 migration |
| Implement epsilon decay logic | 1d | 1 | Exponential/sigmoid curves |
| Implement epsilon-by-urgency (CRITICAL vs NORMAL) | 0.5d | 1 | Simple matching logic |
| Implement exploration logic | 1.5d | 1 | Multi-tier sampling |
| Dashboard widgets (decay progress) | 0.75d | 1 | Filament stats card |
| Testing & validation | 1d | 1 | Unit + integration + decay scenarios |
| Documentation & runbook | 0.5d | 1 | Weekly decay checklist |
| **TOTAL** | **5.25 days** | **1** | Part of Phase 3 |

---

---

## Key Insights: Theory → Practice

### Why Epsilon Decay is Superior to Fixed Epsilon

| Aspect | Fixed Epsilon (e.g., 7%) | Decaying Epsilon (20% → 5%) |
|--------|------------------------|-------------------------------|
| **Phase 1** | Model data sparse; limited learning | Rapid data collection; quick model training |
| **Phase 2** | Model has blind spots | Model sees diverse patterns; steady improvement |
| **Phase 3+** | Unnecessary exploration noise; hurts acceptance rate | Minimal exploration; tight optimization |
| **Adaptability** | Static; doesn't adapt to model maturity | Dynamic; self-adjusting to robustness |

### Why Context-Aware Epsilon Shows Modern Thinking

Traditional ML: "Apply the same model to all scenarios"  
**Your Approach**: "Different urgency = different risk tolerance"

- **CRITICAL**: Time-sensitive, minimal exploration (3%)
- **NORMAL**: Learning-focused with decay (20% → 5%)

This is **context-aware ML** at its finest.

---

## Approval & Next Steps

This document is ready for integration into **Phase 3** of the Predictive Donor Scoring System.

Proposed changes to Phase 3:
1. Add `was_exploration` migration to `request_responses` table
2. Implement `getDecayedEpsilon()` time-based decay
3. Implement `getEpsilonByUrgency()` for CRITICAL (3%) vs NORMAL (decay)
4. Implement `filterDonorsByThresholdWithExploration()` with epsilon-greedy logic
5. Add decay progress widget to Filament dashboard
6. Create comprehensive tests for decay schedule and epsilon logic

**Status**: Ready to integrate ✅
