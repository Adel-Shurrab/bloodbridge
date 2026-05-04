<?php

namespace App\Services;

use App\Enums\BloodType;
use App\Enums\UrgencyLevel;
use App\Models\Achievement;
use App\Models\BloodRequest;
use App\Models\Donor;
use App\Models\DonorAchievement;
use App\Models\RequestResponse;
use App\Enums\RequestResponseStatus;
use Illuminate\Support\Facades\Log;

class AchievementService
{
    /**
     * Called once per COMPLETED donation.
     * Awards base points + urgency bonus, then evaluates all achievements.
     * MUST be called inside a DB::transaction — performs only DB operations.
     */
    public function awardDonationPoints(Donor $donor, BloodRequest $bloodRequest): void
    {
        $points = 50; // base

        if ($bloodRequest->urgency_level === UrgencyLevel::CRITICAL) {
            $points += 25;
        }

        // increment() bypasses $fillable — safe inside a transaction
        $donor->increment('points', $points);

        $this->evaluateAndAward($donor, false, $bloodRequest);
    }

    /**
     * Evaluates all unearned achievements for a donor.
     * Awards any whose criteria are now met.
     * Returns list of newly awarded Achievement models (used by backfill for output).
     *
     * @param  bool  $backfillMode  Skip level recalculation and point awards (backfill only).
     */
    public function evaluateAndAward(
        Donor $donor,
        bool $backfillMode = false,
        ?BloodRequest $bloodRequest = null
    ): array {
        $earnedIds = $donor->donorAchievements()->pluck('achievement_id');

        $pending = Achievement::whereNotIn('id', $earnedIds)
            ->orderBy('display_order')
            ->get();

        $newlyAwarded = [];

        foreach ($pending as $achievement) {
            if ($this->meetsCriteria($donor, $achievement, $bloodRequest)) {
                if (! $backfillMode) {
                    $this->award($donor, $achievement);
                } else {
                    // Backfill: award badge only, no bonus points
                    $this->awardBadgeOnly($donor, $achievement);
                }
                $newlyAwarded[] = $achievement;
            }
        }

        if (! $backfillMode) {
            $this->recalculateLevel($donor);
        }

        return $newlyAwarded;
    }

    /**
     * Determines if a donor currently meets the criteria for a given achievement.
     */
    private function meetsCriteria(
        Donor $donor,
        Achievement $achievement,
        ?BloodRequest $context = null
    ): bool {
        $profile = $donor->healthProfile;

        return match ($achievement->criteria_type) {

            // ── Phase 1 ────────────────────────────────────────────────────────
            Achievement::CRITERIA_DONATIONS =>
                ($profile?->total_donations ?? 0) >= $achievement->criteria_value,

            Achievement::CRITERIA_POINTS =>
                $donor->fresh()->points >= $achievement->criteria_value,

            Achievement::CRITERIA_CRITICAL =>
                DonorAchievement::query()
                    ->whereHas('achievement', fn($q) => $q->where('criteria_type', Achievement::CRITERIA_CRITICAL))
                    ->where('donor_id', $donor->id)
                    ->exists()
                    ? false // already earned one, check freshly
                    : $this->countCriticalDonations($donor) >= $achievement->criteria_value,

            Achievement::CRITERIA_RARE_BLOOD =>
                $this->hasRareBloodType($donor),

            // ── Phase 2 ────────────────────────────────────────────────────────
            Achievement::CRITERIA_ACTIVE_MONTHS =>
                $this->countActiveMonths($donor) >= $achievement->criteria_value,

            Achievement::CRITERIA_ACTIVE_YEARS =>
                $this->countActiveYears($donor) >= $achievement->criteria_value,

            Achievement::CRITERIA_NO_CANCEL =>
                $this->countNoCancelStreak($donor) >= $achievement->criteria_value,

            Achievement::CRITERIA_COMPLETION_RATE =>
                $this->meetsCompletionRateCriteria($donor, $achievement->criteria_value),

            // ── Phase 3 ────────────────────────────────────────────────────────
            Achievement::CRITERIA_RESPONSE_TIME =>
                false, // requires notification timestamp data — implement in Phase 3

            Achievement::CRITERIA_GOVERNORATES =>
                $this->countDistinctGovernorates($donor) >= $achievement->criteria_value,

            Achievement::CRITERIA_SPECIAL_DATE =>
                false, // requires date-pattern checking — implement in Phase 3

            default => false,
        };
    }

    /**
     * Awards a badge AND its bonus points. Uses firstOrCreate for idempotency.
     * Wrapped in try/catch — achievement failure must never roll back a donation.
     */
    public function award(Donor $donor, Achievement $achievement, ?int $awardedBy = null): void
    {
        try {
            $created = DonorAchievement::firstOrCreate(
                ['donor_id' => $donor->id, 'achievement_id' => $achievement->id],
                ['earned_at' => now(), 'awarded_by' => $awardedBy]
            );

            // Only add bonus points if this is a new award (not a duplicate)
            if ($created->wasRecentlyCreated && $achievement->points_rewards > 0) {
                $donor->increment('points', $achievement->points_rewards);
            }
        } catch (\Throwable $e) {
            Log::error('AchievementService::award failed', [
                'donor_id'       => $donor->id,
                'achievement_id' => $achievement->id,
                'error'          => $e->getMessage(),
            ]);
        }
    }

    /**
     * Awards a badge without adding bonus points (used during backfill).
     */
    private function awardBadgeOnly(Donor $donor, Achievement $achievement): void
    {
        try {
            DonorAchievement::firstOrCreate(
                ['donor_id' => $donor->id, 'achievement_id' => $achievement->id],
                ['earned_at' => now(), 'awarded_by' => null]
            );
        } catch (\Throwable $e) {
            Log::error('AchievementService::awardBadgeOnly failed', [
                'donor_id'       => $donor->id,
                'achievement_id' => $achievement->id,
                'error'          => $e->getMessage(),
            ]);
        }
    }

    /**
     * Recalculates and saves donor level based on current points.
     * Calls fresh() because multiple increment() calls have occurred.
     */
    public function recalculateLevel(Donor $donor): void
    {
        $points = (int) $donor->fresh()->points;

        $level = match (true) {
            $points >= 1000 => 5,
            $points >= 600  => 4,
            $points >= 300  => 3,
            $points >= 100  => 2,
            default         => 1,
        };

        $donor->update(['level' => $level]);
    }

    // ── Private helper methods ─────────────────────────────────────────────

    private function countCriticalDonations(Donor $donor): int
    {
        return RequestResponse::query()
            ->where('donor_id', $donor->id)
            ->where('status', RequestResponseStatus::COMPLETED)
            ->whereHas('bloodRequest', fn($q) => $q->where('urgency_level', UrgencyLevel::CRITICAL))
            ->count();
    }

    private function hasRareBloodType(Donor $donor): bool
    {
        $rareTypes = [
            BloodType::A_NEGATIVE,
            BloodType::B_NEGATIVE,
            BloodType::AB_NEGATIVE,
            BloodType::O_NEGATIVE,
        ];
        $bloodType = $donor->healthProfile?->verified_blood_type ?? $donor->healthProfile?->blood_type;
        return $bloodType !== null && in_array($bloodType, $rareTypes, true);
    }

    private function countActiveMonths(Donor $donor): int
    {
        return RequestResponse::query()
            ->where('donor_id', $donor->id)
            ->where('status', RequestResponseStatus::COMPLETED)
            ->selectRaw('DATE_FORMAT(verified_at, "%Y-%m") as month')
            ->pluck('month')
            ->unique()
            ->count();
    }

    private function countActiveYears(Donor $donor): int
    {
        return RequestResponse::query()
            ->where('donor_id', $donor->id)
            ->where('status', RequestResponseStatus::COMPLETED)
            ->selectRaw('YEAR(verified_at) as year')
            ->pluck('year')
            ->unique()
            ->count();
    }

    private function countNoCancelStreak(Donor $donor): int
    {
        // Count COMPLETED responses without any CANCELLED responses in between
        // Simplified: count total completed without having ANY cancellation record
        $hasCancellation = RequestResponse::query()
            ->where('donor_id', $donor->id)
            ->where('status', RequestResponseStatus::DECLINED)
            ->exists();

        if ($hasCancellation) {
            return 0;
        }

        return (int) ($donor->healthProfile?->total_donations ?? 0);
    }

    private function meetsCompletionRateCriteria(Donor $donor, int $minDonations): bool
    {
        $total = RequestResponse::query()
            ->where('donor_id', $donor->id)
            ->whereIn('status', [
                RequestResponseStatus::COMPLETED,
                RequestResponseStatus::DECLINED,
                RequestResponseStatus::NO_SHOW,
                RequestResponseStatus::UNREACHABLE,
            ])
            ->count();

        $completed = RequestResponse::query()
            ->where('donor_id', $donor->id)
            ->where('status', RequestResponseStatus::COMPLETED)
            ->count();

        return $completed >= $minDonations && $total > 0 && ($completed / $total) === 1.0;
    }

    private function countDistinctGovernorates(Donor $donor): int
    {
        return RequestResponse::query()
            ->where('request_responses.donor_id', $donor->id)
            ->where('request_responses.status', RequestResponseStatus::COMPLETED)
            ->join('blood_requests', 'request_responses.blood_request_id', '=', 'blood_requests.id')
            ->join('organizations', 'blood_requests.organization_id', '=', 'organizations.id')
            ->distinct()
            ->count('organizations.governorate_id');
    }
}
