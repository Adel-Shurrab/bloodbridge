<?php

namespace App\Services;

use App\Enums\BloodRequestStatus;
use App\Enums\BloodType;
use App\Enums\RequestResponseStatus;
use App\Enums\UrgencyLevel;
use App\Models\BloodRequest;
use App\Models\Donor;
use App\Notifications\BloodRequestMatchNotification;
use App\Jobs\DispatchBloodRequestNotifications;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BloodRequestBroadcastService
{
    
    private const DONOR_SAFETY_MULTIPLIER_NORMAL = 2.0;
    private const DONOR_SAFETY_MULTIPLIER_CRITICAL = 2.5;
    private const CRITICAL_RADIUS_MULTIPLIER = 3;
    private const RADIUS_EXPANSION_STEP_KM = 5;
    private const MAX_SEARCH_RADIUS_KM = 25;

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
            
            $eligibleDonors = DB::transaction(function () use ($bloodRequest) {
                $donors = $this->findEligibleDonorsWithExpansion($bloodRequest);
                $this->updateBroadcastStatus($bloodRequest);

                return $donors;
            });

            $notificationsQueued = $this->notifyEligibleDonors($bloodRequest, $eligibleDonors);

            Log::info('Blood request broadcasted successfully', [
                'blood_request_id' => $bloodRequest->id,
                'donors_found' => $eligibleDonors->count(),
                'notifications_queued' => $notificationsQueued,
            ]);

            return $eligibleDonors->count();
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
     * Validate that blood request has location data (GPS or governorate)
     *
     * @param BloodRequest $bloodRequest
     * @return bool True if location data is valid
     */
    private function hasValidLocation(BloodRequest $bloodRequest): bool
    {
        
        $hasCoordinates = $bloodRequest->lat !== null
            && $bloodRequest->lng !== null
            && $bloodRequest->search_radius_km > 0;

        $hasGovernorate = $bloodRequest->organization
            && $bloodRequest->organization->governorate_id !== null;

        return $hasCoordinates || $hasGovernorate;
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

        $matchedDonors = collect();
        $excludedDonorIds = []; 
        $expansionAttempts = 0;

        while ($this->shouldContinueExpansion($matchedDonors, $targetDonorCount, $currentRadius)) {
            $newDonors = $this->searchDonorsInRadius(
                $bloodRequest,
                $compatibleBloodTypes,
                $currentRadius,
                $isCritical,
                $excludedDonorIds 
            );

            $matchedDonors = $matchedDonors->merge($newDonors);

            $excludedDonorIds = $matchedDonors->pluck('id')->toArray();

            $this->logExpansionAttempt($bloodRequest, $currentRadius, $matchedDonors, $targetDonorCount, $expansionAttempts, $newDonors->count());

            if ($this->targetDonorCountMet($matchedDonors, $targetDonorCount)) {
                break;
            }

            if ($currentRadius >= self::MAX_SEARCH_RADIUS_KM) {
                break; 
            }

            $currentRadius += self::RADIUS_EXPANSION_STEP_KM;
            $expansionAttempts++;
        }

        $finalDonors = $matchedDonors;
        if (!$this->targetDonorCountMet($matchedDonors, $targetDonorCount) && !$isCritical) {
            $unknownDonors = $this->searchUnknownDonors($bloodRequest, $currentRadius, $isCritical, $excludedDonorIds);
            $finalDonors = $matchedDonors->merge($unknownDonors);

            Log::info('Added UNKNOWN blood type donors as fallback', [
                'blood_request_id' => $bloodRequest->id,
                'matched_donors' => $matchedDonors->count(),
                'unknown_donors' => $unknownDonors->count(),
                'total_donors' => $finalDonors->count(),
            ]);
        }

        $this->saveExpansionResults($bloodRequest, $currentRadius);
        $this->logExpansionCompletion($bloodRequest, $currentRadius, $finalDonors, $targetDonorCount, $expansionAttempts);

        return $finalDonors;
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
     * @param array<int> $excludedDonorIds IDs of donors to exclude from search
     * @return Collection<Donor>
     */
    private function searchDonorsInRadius(
        BloodRequest $bloodRequest,
        array $compatibleBloodTypes,
        int $radiusKm,
        bool $isCritical,
        array $excludedDonorIds = []
    ): Collection {
        $cooldownHours = $this->getNotificationCooldownHours($isCritical);

        $lat = $bloodRequest->lat;
        $lng = $bloodRequest->lng;
        $governorateId = $bloodRequest->organization->governorate_id;

        $query = Donor::withinRadius(
            $lat, 
            $lng, 
            $radiusKm,
            $governorateId
        )
            ->compatibleWith($compatibleBloodTypes)
            ->eligible()
            ->whereDoesntHave('eligibilityLogs', fn($q) => $this->applyPermanentExclusionFilter($q))
            ->whereDoesntHave('responses', fn($q) => $this->applyRecentNotificationFilter($q, $cooldownHours));

        if (!empty($excludedDonorIds)) {
            $query->whereNotIn('donors.id', $excludedDonorIds);
        }

        return $query->get();
    }

    /**
     * Search for UNKNOWN blood type donors as fallback (NORMAL requests only)
     *
     * @param BloodRequest $bloodRequest
     * @param int $radiusKm Search radius in kilometers
     * @param bool $isCritical
     * @param array<int> $excludedDonorIds IDs of donors to exclude from search
     * @return Collection<Donor>
     */
    private function searchUnknownDonors(
        BloodRequest $bloodRequest,
        int $radiusKm,
        bool $isCritical,
        array $excludedDonorIds = []
    ): Collection {
        
        if ($isCritical) {
            return collect([]);
        }

        $cooldownHours = $this->getNotificationCooldownHours($isCritical);
        $lat = $bloodRequest->lat;
        $lng = $bloodRequest->lng;
        $governorateId = $bloodRequest->organization->governorate_id;

        $query = Donor::withinRadius($lat, $lng, $radiusKm, $governorateId)
            ->eligible()
            ->whereHas('healthProfile', function ($query) {
                
                $query->where('blood_type', BloodType::UNKNOWN)
                    ->whereNull('verified_blood_type');
            })
            ->whereDoesntHave('eligibilityLogs', fn($q) => $this->applyPermanentExclusionFilter($q))
            ->whereDoesntHave('responses', fn($q) => $this->applyRecentNotificationFilter($q, $cooldownHours));

        if (!empty($excludedDonorIds)) {
            $query->whereNotIn('donors.id', $excludedDonorIds);
        }

        return $query->get();
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
     * Dispatch notifications to eligible donors using background job batching.
     * 
     * This prevents blocking the user and handles large donor lists efficiently.
     *
     * @param BloodRequest $bloodRequest
     * @param Collection<Donor> $donors
     * @return int Number of donors queued for notification
     */
    private function notifyEligibleDonors(BloodRequest $bloodRequest, Collection $donors): int
    {
        
        $donorData = [];
        foreach ($donors as $donor) {
            if ($donor->user) {
                $donorData[$donor->user->id] = $donor->distance ?? null;
            }
        }

        if (!empty($donorData)) {
            DispatchBloodRequestNotifications::dispatchBatches($bloodRequest->id, $donorData);
        }

        return count($donorData);
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
        int $attempt,
        int $newDonorsCount
    ): void {
        Log::info('Radius expansion attempt', [
            'blood_request_id' => $bloodRequest->id,
            'current_radius' => $radius,
            'total_donors_found' => $donors->count(),
            'new_donors_this_iteration' => $newDonorsCount,
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
