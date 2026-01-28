<?php

namespace App\Services;

use App\Enums\BloodRequestStatus;
use App\Enums\RequestResponseStatus;
use App\Enums\UrgencyLevel;
use App\Models\BloodRequest;
use App\Models\Donor;
use App\Notifications\BloodRequestMatchNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BloodRequestBroadcastService
{
    // Constants for expansion configuration
    private const DONOR_SAFETY_MULTIPLIER_NORMAL = 2.0;
    private const DONOR_SAFETY_MULTIPLIER_CRITICAL = 2.5;
    private const CRITICAL_RADIUS_MULTIPLIER = 3;
    private const RADIUS_EXPANSION_STEP_KM = 5;
    private const MAX_SEARCH_RADIUS_KM = 25;

    // Constants for notification cooldown (in hours)
    private const NOTIFICATION_COOLDOWN_CRITICAL_HOURS = 0.5;
    private const NOTIFICATION_COOLDOWN_NORMAL_HOURS = 2.0;

    /**
     * Broadcast a blood request to eligible donors within progressive search radius
     *
     * @param BloodRequest $bloodRequest The blood request to broadcast
     * @return int Number of eligible donors found and notified
     * @throws \Exception If broadcasting fails
     */
    public function broadcast(BloodRequest $bloodRequest): int
    {
        if (!$this->hasValidLocation($bloodRequest)) {
            Log::warning('Blood request missing required location data', [
                'blood_request_id' => $bloodRequest->id,
            ]);
            return 0;
        }

        try {
            return DB::transaction(function () use ($bloodRequest) {
                $eligibleDonors = $this->findEligibleDonorsWithExpansion($bloodRequest);
                $notificationsSent = $this->notifyEligibleDonors($bloodRequest, $eligibleDonors);
                $this->updateBroadcastStatus($bloodRequest);

                Log::info('Blood request broadcasted successfully', [
                    'blood_request_id' => $bloodRequest->id,
                    'donors_found' => $eligibleDonors->count(),
                    'notifications_sent' => $notificationsSent,
                ]);

                return $eligibleDonors->count();
            });
        } catch (\Exception $e) {
            Log::error('Failed to broadcast blood request', [
                'blood_request_id' => $bloodRequest->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Validate that blood request has required location data
     *
     * @param BloodRequest $bloodRequest
     * @return bool True if location data is valid
     */
    private function hasValidLocation(BloodRequest $bloodRequest): bool
    {
        return $bloodRequest->lat !== null
            && $bloodRequest->lng !== null
            && $bloodRequest->search_radius_km > 0;
    }

    /**
     * Find eligible donors using progressive radius expansion
     *
     * @param BloodRequest $bloodRequest
     * @return Collection<Donor> Collection of eligible donors
     */
    private function findEligibleDonorsWithExpansion(BloodRequest $bloodRequest): Collection
    {
        $compatibleBloodTypes = $bloodRequest->blood_type->getCompatibleDonorTypes();
        $isCritical = $this->isCriticalRequest($bloodRequest);
        $targetDonorCount = $this->calculateTargetDonorCount($bloodRequest, $isCritical);
        $currentRadius = $this->getInitialSearchRadius($bloodRequest, $isCritical);

        $donors = collect();
        $expansionAttempts = 0;

        // Progressive expansion loop
        while ($this->shouldContinueExpansion($donors, $targetDonorCount, $currentRadius)) {
            $donors = $this->searchDonorsInRadius(
                $bloodRequest,
                $compatibleBloodTypes,
                $currentRadius,
                $isCritical
            );

            $this->logExpansionAttempt($bloodRequest, $currentRadius, $donors, $targetDonorCount, $expansionAttempts);

            if ($this->targetDonorCountMet($donors, $targetDonorCount)) {
                break;
            }

            $currentRadius += self::RADIUS_EXPANSION_STEP_KM;
            $expansionAttempts++;
        }

        $this->saveExpansionResults($bloodRequest, $currentRadius);
        $this->logExpansionCompletion($bloodRequest, $currentRadius, $donors, $targetDonorCount, $expansionAttempts);

        return $donors;
    }

    /**
     * Determine if request is critical urgency
     *
     * @param BloodRequest $bloodRequest
     * @return bool
     */
    private function isCriticalRequest(BloodRequest $bloodRequest): bool
    {
        return $bloodRequest->urgency_level === UrgencyLevel::CRITICAL;
    }

    /**
     * Calculate target number of donors needed (with safety margin)
     *
     * @param BloodRequest $bloodRequest
     * @param bool $isCritical
     * @return int Target donor count
     */
    private function calculateTargetDonorCount(BloodRequest $bloodRequest, bool $isCritical): int
    {
        $multiplier = $isCritical
            ? self::DONOR_SAFETY_MULTIPLIER_CRITICAL
            : self::DONOR_SAFETY_MULTIPLIER_NORMAL;

        return (int) ceil($bloodRequest->units_needed * $multiplier);
    }

    /**
     * Get initial search radius (multiplied for critical requests)
     *
     * @param BloodRequest $bloodRequest
     * @param bool $isCritical
     * @return int Initial radius in kilometers
     */
    private function getInitialSearchRadius(BloodRequest $bloodRequest, bool $isCritical): int
    {
        return $isCritical
            ? $bloodRequest->search_radius_km * self::CRITICAL_RADIUS_MULTIPLIER
            : $bloodRequest->search_radius_km;
    }

    /**
     * Determine if expansion should continue
     *
     * @param Collection $donors
     * @param int $targetCount
     * @param int $currentRadius
     * @return bool
     */
    private function shouldContinueExpansion(Collection $donors, int $targetCount, int $currentRadius): bool
    {
        return $donors->count() < $targetCount && $currentRadius <= self::MAX_SEARCH_RADIUS_KM;
    }

    /**
     * Check if target donor count has been met
     *
     * @param Collection $donors
     * @param int $targetCount
     * @return bool
     */
    private function targetDonorCountMet(Collection $donors, int $targetCount): bool
    {
        return $donors->count() >= $targetCount;
    }

    /**
     * Search for eligible donors within a specific radius
     *
     * @param BloodRequest $bloodRequest
     * @param array<int> $compatibleBloodTypes
     * @param int $radiusKm Search radius in kilometers
     * @param bool $isCritical
     * @return Collection<Donor>
     */
    private function searchDonorsInRadius(
        BloodRequest $bloodRequest,
        array $compatibleBloodTypes,
        int $radiusKm,
        bool $isCritical
    ): Collection {
        $cooldownHours = $this->getNotificationCooldownHours($isCritical);

        return Donor::withinRadius(
            $bloodRequest->lat,
            $bloodRequest->lng,
            $radiusKm,
            $bloodRequest->organization->governorate_id
        )
            ->whereHas('healthProfile', function ($query) use ($compatibleBloodTypes) {
                $this->applyBloodTypeFilter($query, $compatibleBloodTypes);
                $this->applyEligibilityFilter($query);
            })
            ->whereDoesntHave('eligibilityLogs', fn($q) => $this->applyPermanentExclusionFilter($q))
            ->whereDoesntHave('responses', fn($q) => $this->applyRecentNotificationFilter($q, $cooldownHours))
            ->get();
    }

    /**
     * Get notification cooldown period based on urgency
     *
     * @param bool $isCritical
     * @return float Hours to wait before re-notification
     */
    private function getNotificationCooldownHours(bool $isCritical): float
    {
        return $isCritical
            ? self::NOTIFICATION_COOLDOWN_CRITICAL_HOURS
            : self::NOTIFICATION_COOLDOWN_NORMAL_HOURS;
    }

    /**
     * Apply blood type compatibility filter to query
     */
    private function applyBloodTypeFilter($query, array $compatibleBloodTypes): void
    {
        $query->where(function ($q) use ($compatibleBloodTypes) {
            $q->whereIn('verified_blood_type', $compatibleBloodTypes)
                ->orWhere(function ($q2) use ($compatibleBloodTypes) {
                    $q2->whereNull('verified_blood_type')
                        ->whereIn('blood_type', $compatibleBloodTypes);
                });
        });
    }

    /**
     * Apply donor eligibility filter to query
     */
    private function applyEligibilityFilter($query): void
    {
        $query->where('is_eligible', true)
            ->where(function ($q) {
                $q->whereNull('next_eligible_date')
                    ->orWhereDate('next_eligible_date', '<=', now());
            });
    }

    /**
     * Apply permanent exclusion filter to query
     */
    private function applyPermanentExclusionFilter($query): void
    {
        $query->where('is_eligible', false)
            ->where('is_permanent', true);
    }

    /**
     * Apply recent notification filter to query (prevents spam)
     */
    private function applyRecentNotificationFilter($query, float $cooldownHours): void
    {
        $query->where('created_at', '>=', now()->subHours($cooldownHours))
            ->whereIn('status', [
                RequestResponseStatus::PENDING,
                RequestResponseStatus::ACCEPTED,
            ]);
    }

    /**
     * Send notifications to all eligible donors
     *
     * @param BloodRequest $bloodRequest
     * @param Collection<Donor> $donors
     * @return int Number of notifications sent
     */
    private function notifyEligibleDonors(BloodRequest $bloodRequest, Collection $donors): int
    {
        $notificationsSent = 0;

        foreach ($donors as $donor) {
            if ($donor->user) {
                $distance = $donor->distance ?? null;
                $donor->user->notify(new BloodRequestMatchNotification($bloodRequest, $distance));
                $notificationsSent++;
            }
        }

        return $notificationsSent;
    }

    /**
     * Save final expanded radius to blood request
     *
     * @param BloodRequest $bloodRequest
     * @param int $finalRadius
     */
    private function saveExpansionResults(BloodRequest $bloodRequest, int $finalRadius): void
    {
        $bloodRequest->actual_search_radius_km = $finalRadius;
        $bloodRequest->save();
    }

    /**
     * Update blood request status to broadcasted
     *
     * @param BloodRequest $bloodRequest
     */
    private function updateBroadcastStatus(BloodRequest $bloodRequest): void
    {
        $bloodRequest->status = BloodRequestStatus::BROADCASTED;
        $bloodRequest->broadcasted_at = now();
        $bloodRequest->save();
    }

    /**
     * Log an expansion attempt
     */
    private function logExpansionAttempt(
        BloodRequest $bloodRequest,
        int $radius,
        Collection $donors,
        int $target,
        int $attempt
    ): void {
        Log::info('Radius expansion attempt', [
            'blood_request_id' => $bloodRequest->id,
            'current_radius' => $radius,
            'donors_found' => $donors->count(),
            'target' => $target,
            'expansion_attempt' => $attempt,
        ]);
    }

    /**
     * Log expansion completion
     */
    private function logExpansionCompletion(
        BloodRequest $bloodRequest,
        int $finalRadius,
        Collection $donors,
        int $target,
        int $attempts
    ): void {
        Log::info('Progressive expansion completed', [
            'blood_request_id' => $bloodRequest->id,
            'initial_radius' => $bloodRequest->search_radius_km,
            'final_radius' => $finalRadius,
            'expansion_attempts' => $attempts,
            'donors_found' => $donors->count(),
            'target' => $target,
            'met_target' => $donors->count() >= $target,
        ]);
    }
}
