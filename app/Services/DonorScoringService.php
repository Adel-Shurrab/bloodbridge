<?php

namespace App\Services;

use App\DataTransferObjects\ScoringResult;
use App\Enums\RequestResponseStatus;
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
        // Build [donor_id => distance_km] from the distance column added by withinRadius scope
        $distances = $donors
            ->filter(fn(Donor $d) => $d->getAttribute('distance') !== null)
            ->mapWithKeys(fn(Donor $d) => [(int) $d->id => (float) $d->getAttribute('distance')])
            ->toArray();

        // Step 1: Get a ScoringResult for every donor via the waterfall
        $results = $this->getScoreResults($donors->pluck('id')->toArray(), $urgency, $distances);

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

        // Step 5: Fill slots, then backfill from whichever pool still has capacity.
        $sortedExploiters = $exploiters->sortByDesc('score')->values();
        $shuffledExplorers = $explorers->shuffle()->values();

        $selectedExploiters = $sortedExploiters->take($exploitSlots);
        $selectedExplorers = $shuffledExplorers->take($exploreSlots);
        $selected = $selectedExploiters->merge($selectedExplorers);

        $remainingBudget = $budget - $selected->count();

        if ($remainingBudget > 0) {
            $selectedIds = $selected->pluck('id')->all();

            $backfill = $sortedExploiters
                ->merge($shuffledExplorers)
                ->reject(fn(Donor $donor) => in_array($donor->id, $selectedIds, true))
                ->take($remainingBudget);

            $selected = $selected->merge($backfill);
        }

        $explorerIds = $shuffledExplorers->pluck('id');
        $selectedExplorerCount = $selected
            ->filter(fn(Donor $donor) => $explorerIds->contains($donor->id))
            ->count();
        $selectedExploiterCount = $selected->count() - $selectedExplorerCount;

        // Step 6: Build stats for logging and A/B tracking
        $coldStartCount = $scored->filter(fn($d) => $d->scoringResult->isColdStart)->count();
        $sourceBreakdown = $scored
            ->groupBy(fn($d) => $d->scoringResult->source)
            ->map->count()
            ->toArray();

        Log::info('DonorScoringService::scoreAndSelect', [
            'total_eligible' => $donors->count(),
            'exploiters_pool' => $exploiters->count(),
            'explorers_pool' => $explorers->count(),
            'selected_total' => $selected->count(),
            'cold_start' => $coldStartCount,
            'budget' => $budget,
            'urgency' => $urgency,
            'sources' => $sourceBreakdown,
        ]);

        return [
            'selected' => $selected->values(),
            'exploiter_count' => $selectedExploiterCount,
            'explorer_count' => $selectedExplorerCount,
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
        $results = $this->getScoreResults([(int) $donor->id], 'normal', []);

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
    // Waterfall - Never Fails Silently
    // =========================================================================

    /**
     * The scoring waterfall.
     * Tries each level in order, falls back to the next if unavailable.
     *
     * Level 1: Fresh DB cache
     * Level 2: FastAPI (XGBoost) - skipped if ml_scoring_enabled = false
     * Level 3: Rule-based PHP formula
     * Level 4: Neutral 0.5 (implicit - handled in scoreAndSelect)
     *
     * @param  int[] $donorIds
     * @return array<int, ScoringResult>
     */
    private function getScoreResults(array $donorIds, string $urgency, array $distances): array
    {
        // Level 1: Fresh DB cache
        $results = $this->getFromDbCache($donorIds);
        $missing = array_values(array_diff($donorIds, array_keys($results)));

        if (empty($missing)) {
            return $results;
        }

        // Level 2: FastAPI
        if ($this->settings->ml_scoring_enabled) {
            $apiResults = $this->getFromFastApi($missing, $urgency, $distances);
            $results = $results + $apiResults;
            $missing = array_values(array_diff($donorIds, array_keys($results)));
        }

        if (empty($missing)) {
            $this->persistToDbCache($results);
            return $results;
        }

        // Level 3: Rule-based
        Log::info('Using rule-based scoring for ' . count($missing) . ' donors');
        $ruleResults = $this->getFromRuleBasedQuery($missing);
        $results = $results + $ruleResults;

        $this->persistToDbCache($results);

        return $results;
    }

    /**
     * Level 1: Get fresh scores from donor_predictive_scores table.
     * A score is considered fresh if computed within score_staleness_days.
     *
     * @param  int[] $donorIds
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
     * Returns empty array if FastAPI is unavailable - waterfall continues.
     *
     * @param  int[] $donorIds
     * @return array<int, ScoringResult>
     */
    private function getFromFastApi(array $donorIds, string $urgency, array $distances): array
    {
        $raw = $this->circuitBreaker->attempt(function () use ($donorIds, $urgency, $distances) {
            $response = Http::connectTimeout(5)
                ->timeout(8)
                ->post(config('services.fastapi.url') . '/api/score', [
                    'donor_ids' => $donorIds,
                    'urgency'   => $urgency,
                    'distances' => empty($distances) ? null : $distances,
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
     * No loops. No N+1. Always available - no external dependencies.
     *
     * Formula:
     *   score = (acceptance_rate x 0.50)
     *         + (recency_score   x 0.30)
     *         + (loyalty_score   x 0.20)
     *
     * @param  int[] $donorIds
     * @return array<int, ScoringResult>
     */
    private function getFromRuleBasedQuery(array $donorIds): array
    {
        $minHistory = $this->settings->min_history_for_exploitation;

        $acceptedValue = RequestResponseStatus::ACCEPTED->value;
        $completedValue = RequestResponseStatus::COMPLETED->value;
        $noShowValue = RequestResponseStatus::NO_SHOW->value;
        $countedStatuses = [
            RequestResponseStatus::PENDING->value,
            RequestResponseStatus::ACCEPTED->value,
            RequestResponseStatus::COMPLETED->value,
            RequestResponseStatus::IGNORED->value,
            RequestResponseStatus::NO_SHOW->value,
            RequestResponseStatus::UNREACHABLE->value,
            RequestResponseStatus::NOT_NEEDED->value,
        ];

        $rows = DB::table('donors as d')
            ->select([
                'd.id as donor_id',
                DB::raw('COUNT(rr.id) as total_responses'),
                DB::raw("COUNT(CASE WHEN rr.status IN ({$acceptedValue}, {$completedValue}) THEN 1 END) as accepted_count"),
                DB::raw("COUNT(CASE WHEN rr.status = {$noShowValue} THEN 1 END) as no_show_count"),
                DB::raw('MAX(rr.responded_at) as last_responded_at'),
                DB::raw('COALESCE(dhp.total_donations, 0) as total_donations'),
            ])
            ->leftJoin('request_responses as rr', function ($join) use ($countedStatuses) {
                $join->on('d.id', '=', 'rr.donor_id')
                    ->whereIn('rr.status', $countedStatuses)
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
            $total = (int) $row->total_responses;

            if ($total < $minHistory) {
                $results[$donorId] = ScoringResult::coldStart($donorId);
                continue;
            }

            $noShowPenalty = (int) $row->no_show_count;
            $adjustedTotal = $total + $noShowPenalty;

            $acceptanceRate = $row->accepted_count / $adjustedTotal;

            $daysSinceLast = $row->last_responded_at
                ? (int) now()->diffInDays(\Carbon\Carbon::parse($row->last_responded_at))
                : 999;
            $recencyScore = match (true) {
                $daysSinceLast <= 7 => 1.0,
                $daysSinceLast <= 30 => 0.8,
                $daysSinceLast <= 90 => 0.5,
                $daysSinceLast <= 180 => 0.3,
                default => 0.1,
            };

            $loyaltyScore = min((int) $row->total_donations / 10, 1.0);

            $score = round(
                ($acceptanceRate * 0.50) +
                    ($recencyScore * 0.30) +
                    ($loyaltyScore * 0.20),
                4
            );

            $results[$donorId] = ScoringResult::fromModel($donorId, $score, 'rule_based');
        }

        return $results;
    }

    /**
     * Persist newly computed scores (Level 2 and Level 3) to the DB cache.
     * Skips cold-start placeholders and results that were already read from the cache.
     *
     * @param array<int, ScoringResult> $results
     */
    private function persistToDbCache(array $results): void
    {
        $rows = [];
        $now  = now();

        foreach ($results as $donorId => $result) {
            if ($result->isColdStart || $result->source === 'db_cache') {
                continue;
            }

            $rows[] = [
                'donor_id'               => $donorId,
                'acceptance_probability' => $result->score,
                'computed_at'            => $now,
                'model_version'          => $result->source,
                'data_points_count'      => 0,
                'created_at'             => $now,
                'updated_at'             => $now,
            ];
        }

        if (empty($rows)) {
            return;
        }

        DonorPredictiveScore::upsert(
            $rows,
            ['donor_id'],
            ['acceptance_probability', 'computed_at', 'model_version', 'updated_at']
        );
    }

    // =========================================================================
    // Epsilon-Greedy Bucketing
    // =========================================================================

    private function splitByEpsilonGreedy(Collection $scored): array
    {
        // Cold-start donors always go to exploration
        $coldStart = $scored->filter(fn($d) => $d->scoringResult->isColdStart);

        // Scored donors sorted high -> low
        $withScores = $scored
            ->filter(fn($d) => ! $d->scoringResult->isColdStart)
            ->sortByDesc('score')
            ->values();

        $epsilon = $this->settings->exploration_ratio;
        $exploreCount = (int) ceil($withScores->count() * $epsilon);

        $exploiters = $withScores->slice(0, $withScores->count() - $exploreCount);
        $lowScorers = $withScores->slice($withScores->count() - $exploreCount);

        // Merge cold-start + low scorers into one exploration pool
        $explorers = $coldStart->merge($lowScorers);

        return [$exploiters->values(), $explorers->values()];
    }
}
