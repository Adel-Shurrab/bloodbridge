<?php

namespace App\Services;

use App\DataTransferObjects\ScoringResult;
use App\Models\Donor;
use App\Models\DonorPredictiveScore;
use App\Settings\ScoringSettings;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DonorScoringService
{
    public function __construct(
        private ScoringSettings $settings,
        private FastApiCircuitBreaker $circuitBreaker,
    ) {}

    // =========================================================================
    // Public API
    // =========================================================================

    /**
     * Main entry point.
     * Takes eligible donors, scores them, applies epsilon-greedy selection,
     * and enforces the notification budget cap.
     *
     * @param  Collection<Donor> $donors    Eligible donors for this blood request
     * @param  string            $urgency   'normal' | 'urgent' | 'critical'
     * @return array{
     *   selected: Collection,
     *   exploiter_count: int,
     *   explorer_count: int,
     *   cold_start_count: int,
     *   source_breakdown: array
     * }
     */
    public function scoreAndSelect(Collection $donors, string $urgency): array
    {
        // Step 1: Get a ScoringResult for every donor via the waterfall
        $results = $this->getScoreResults($donors->pluck('id')->toArray());

        // Step 2: Attach the ScoringResult and score to each Donor model
        $scored = $donors->map(function (Donor $donor) use ($results) {
            $result = $results[$donor->id] ?? ScoringResult::neutral($donor->id);
            $donor->setAttribute('scoringResult', $result);
            $donor->setAttribute('score', $result->score);
            return $donor;
        });

        // Step 3: Split into exploitation and exploration pools
        [$exploiters, $explorers] = $this->splitByEpsilonGreedy($scored);

        // Step 4: Apply budget cap
        // Critical requests get 50% more slots because lives are at stake
        $budget = $this->settings->max_notifications_per_broadcast;
        if (strtolower($urgency) === 'critical') {
            $budget = (int) ($budget * 1.5);
        }

        $exploitSlots = (int) ceil($budget * (1 - $this->settings->exploration_ratio));
        $exploreSlots = $budget - $exploitSlots;

        // Step 5: Fill slots
        $selectedExploiters = $exploiters->sortByDesc('score')->take($exploitSlots);
        $selectedExplorers  = $explorers->shuffle()->take($exploreSlots);
        $selected           = $selectedExploiters->merge($selectedExplorers);

        // Step 6: Build stats for logging and A/B tracking
        $coldStartCount  = $scored->filter(fn($d) => $d->scoringResult->isColdStart)->count();
        $sourceBreakdown = $scored
            ->groupBy(fn($d) => $d->scoringResult->source)
            ->map->count()
            ->toArray();

        Log::info('DonorScoringService::scoreAndSelect', [
            'total_eligible'  => $donors->count(),
            'exploiters_pool' => $exploiters->count(),
            'explorers_pool'  => $explorers->count(),
            'selected_total'  => $selected->count(),
            'cold_start'      => $coldStartCount,
            'budget'          => $budget,
            'urgency'         => $urgency,
            'sources'         => $sourceBreakdown,
        ]);

        return [
            'selected'         => $selected->values(),
            'exploiter_count'  => $selectedExploiters->count(),
            'explorer_count'   => $selectedExplorers->count(),
            'cold_start_count' => $coldStartCount,
            'source_breakdown' => $sourceBreakdown,
        ];
    }

    /**
     * Get score for a single donor.
     * Used for testing and manual checks.
     */
    public function getScore(Donor $donor): ScoringResult
    {
        $results = $this->getScoreResults([(int) $donor->id]);

        return $results[(int) $donor->id]
            ?? ScoringResult::neutral($donor->id);
    }

    /**
     * Trigger model retraining in the FastAPI service.
     *
     * @return array<mixed>
     */
    public function triggerRetraining(): array
    {
        $response = Http::connectTimeout(5)
            ->timeout(30)
            ->post(config('services.fastapi.url') . '/api/retrain');

        if (! $response->successful()) {
            throw new \Exception('/api/retrain returned HTTP ' . $response->status());
        }

        return $response->json() ?? [];
    }

    // =========================================================================
    // Waterfall — Never Fails Silently
    // =========================================================================

    /**
     * The scoring waterfall.
     * Tries each level in order, falls back to the next if unavailable.
     *
     * Level 1: Fresh DB cache
     * Level 2: FastAPI (XGBoost) — skipped if ml_scoring_enabled = false
     * Level 3: Rule-based PHP formula
     * Level 4: Neutral 0.5 (implicit — handled in scoreAndSelect)
     *
     * @param  int[]                     $donorIds
     * @return array<int, ScoringResult>
     */
    private function getScoreResults(array $donorIds): array
    {
        // Level 1: Fresh DB cache
        $results = $this->getFromDbCache($donorIds);
        $missing = array_values(array_diff($donorIds, array_keys($results)));

        if (empty($missing)) {
            return $results;
        }

        // Level 2: FastAPI
        if ($this->settings->ml_scoring_enabled) {
            $apiResults = $this->getFromFastApi($missing);
            $results = $results + $apiResults;
            $missing    = array_values(array_diff($donorIds, array_keys($results)));
        }

        if (empty($missing)) {
            return $results;
        }

        // Level 3: Rule-based
        Log::info('Using rule-based scoring for ' . count($missing) . ' donors');
        $ruleResults = $this->getFromRuleBasedQuery($missing);

        return $results + $ruleResults;
    }

    /**
     * Level 1: Get fresh scores from donor_predictive_scores table.
     * A score is considered fresh if computed within score_staleness_days.
     *
     * @param  int[]                     $donorIds
     * @return array<int, ScoringResult>
     */
    private function getFromDbCache(array $donorIds): array
    {
        return DonorPredictiveScore::whereIn('donor_id', $donorIds)
            ->where('computed_at', '>=', now()->subDays($this->settings->score_staleness_days))
            ->get()
            ->mapWithKeys(fn($row) => [
                $row->donor_id => ScoringResult::fromModel(
                    $row->donor_id,
                    (float) $row->acceptance_probability,
                    'db_cache'
                ),
            ])
            ->toArray();
    }

    /**
     * Level 2: Get scores from FastAPI (XGBoost model).
     * Returns empty array if FastAPI is unavailable — waterfall continues.
     *
     * @param  int[]                     $donorIds
     * @return array<int, ScoringResult>
     */
    private function getFromFastApi(array $donorIds): array
    {
        $raw = $this->circuitBreaker->attempt(function () use ($donorIds) {
            $response = Http::connectTimeout(5)
                ->timeout(8)
                ->post(config('services.fastapi.url') . '/api/score', [
                    'donor_ids' => $donorIds,
                ]);

            if (! $response->successful()) {
                throw new \Exception('/score returned HTTP ' . $response->status());
            }

            return $response->json('scores', []);
        });

        if ($raw === null) {
            return [];
        }

        $results = [];
        foreach ($raw as $donorId => $scoreData) {
            $isColdStart = (bool) ($scoreData['is_cold_start'] ?? false);

            $results[(int) $donorId] = $isColdStart
                ? ScoringResult::coldStart((int) $donorId)
                : ScoringResult::fromModel(
                    (int) $donorId,
                    (float) $scoreData['score'],
                    'fastapi'
                );
        }

        return $results;
    }

    /**
     * Level 3: Rule-based scoring using a single aggregate SQL query.
     * No loops. No N+1. Always available — no external dependencies.
     *
     * Formula:
     *   score = (acceptance_rate × 0.50)
     *         + (recency_score   × 0.30)
     *         + (loyalty_score   × 0.20)
     *
     * @param  int[]                     $donorIds
     * @return array<int, ScoringResult>
     */
    private function getFromRuleBasedQuery(array $donorIds): array
    {
        $minHistory = $this->settings->min_history_for_exploitation;

        $rows = DB::table('donors as d')
            ->select([
                'd.id as donor_id',
                DB::raw('COUNT(rr.id) as total_responses'),
                DB::raw('COUNT(CASE WHEN rr.status IN (1, 3) THEN 1 END) as accepted_count'),
                DB::raw('COUNT(CASE WHEN rr.status = 5 THEN 1 END) as no_show_count'),
                DB::raw('DATEDIFF(NOW(), MAX(rr.responded_at)) as days_since_last'),
                DB::raw('COALESCE(dhp.total_donations, 0) as total_donations'),
            ])
            ->leftJoin('request_responses as rr', function ($join) {
                $join->on('d.id', '=', 'rr.donor_id')
                    ->whereIn('rr.status', [1, 2, 3, 4, 5, 6, 7])
                    ->whereNotNull('rr.responded_at');
            })
            ->leftJoin('donor_health_profiles as dhp', 'd.id', '=', 'dhp.donor_id')
            ->whereIn('d.id', $donorIds)
            ->whereNull('d.deleted_at')
            ->groupBy('d.id', 'dhp.total_donations')
            ->get();

        $results = [];

        foreach ($rows as $row) {
            $donorId = (int) $row->donor_id;
            $total   = (int) $row->total_responses;

            if ($total < $minHistory) {
                $results[$donorId] = ScoringResult::coldStart($donorId);
                continue;
            }

            $acceptanceRate = $row->accepted_count / $total;
            $noShowPenalty  = (int) $row->no_show_count;
            $adjustedTotal  = $total + $noShowPenalty;

            $acceptanceRate = $row->accepted_count / $adjustedTotal;

            $daysSinceLast  = $row->days_since_last ?? 999;
            $recencyScore   = match (true) {
                $daysSinceLast <= 7   => 1.0,
                $daysSinceLast <= 30  => 0.8,
                $daysSinceLast <= 90  => 0.5,
                $daysSinceLast <= 180 => 0.3,
                default               => 0.1,
            };

            $loyaltyScore = min((int) $row->total_donations / 10, 1.0);

            $score = round(
                ($acceptanceRate * 0.50) +
                    ($recencyScore   * 0.30) +
                    ($loyaltyScore   * 0.20),
                4
            );

            $results[$donorId] = ScoringResult::fromModel($donorId, $score, 'rule_based');
        }

        return $results;
    }

    // =========================================================================
    // Epsilon-Greedy Bucketing
    // =========================================================================

    /**
     * Split scored donors into two pools:
     *
     * Exploiters: donors the system is confident about (high scorers)
     *             → Fill 80% of notification slots
     *
     * Explorers:  cold-start donors + bottom epsilon% of scored donors
     *             → Fill 20% of notification slots
     *             → Helps discover hidden high-potential donors
     *
     * @param  Collection $scored Donors with scoringResult and score attributes
     * @return array{0: Collection, 1: Collection} [exploiters, explorers]
     */
    private function splitByEpsilonGreedy(Collection $scored): array
    {
        // Cold-start donors always go to exploration
        $coldStart  = $scored->filter(fn($d) => $d->scoringResult->isColdStart);

        // Scored donors sorted high → low
        $withScores = $scored
            ->filter(fn($d) => ! $d->scoringResult->isColdStart)
            ->sortByDesc('score')
            ->values();

        $epsilon      = $this->settings->exploration_ratio;
        $exploreCount = (int) ceil($withScores->count() * $epsilon);

        $exploiters = $withScores->slice(0, $withScores->count() - $exploreCount);
        $lowScorers = $withScores->slice($withScores->count() - $exploreCount);

        // Merge cold-start + low scorers into one exploration pool
        $explorers = $coldStart->merge($lowScorers);

        return [$exploiters->values(), $explorers->values()];
    }
}
