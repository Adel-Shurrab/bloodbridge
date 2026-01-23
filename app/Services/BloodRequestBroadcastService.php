<?php

namespace App\Services;

use App\Models\BloodRequest;
use App\Models\Donor;
use App\Helpers\GeoHelper;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BloodRequestBroadcastService
{
    /**
     * Broadcast a blood request to eligible donors
     *
     * @param BloodRequest $bloodRequest
     * @return int Number of donors found
     * @throws \Exception
     */
    public function broadcast(BloodRequest $bloodRequest): int
    {
        // Validate location data
        if (!$this->hasValidLocation($bloodRequest)) {
            Log::warning('Blood request missing location data', [
                'blood_request_id' => $bloodRequest->id,
            ]);
            return 0;
        }

        try {
            return DB::transaction(function () use ($bloodRequest) {
                // Find eligible donors
                $eligibleDonors = $this->findEligibleDonors($bloodRequest);
                $donorsCount = $eligibleDonors->count();

                // Send notifications to matched donors
                $notifiedCount = 0;
                foreach ($eligibleDonors as $donor) {
                    // Only notify donors who have user accounts
                    if ($donor->user) {
                        $distance = $donor->distance ?? null; // Distance from scopeWithinRadius
                        $donor->user->notify(new \App\Notifications\BloodRequestMatchNotification($bloodRequest, $distance));
                        $notifiedCount++;
                    }
                }

                // Update blood request status
                $this->updateBroadcastStatus($bloodRequest);

                Log::info('Blood request broadcasted successfully', [
                    'blood_request_id' => $bloodRequest->id,
                    'donors_found' => $donorsCount,
                    'notifications_sent' => $notifiedCount,
                ]);

                return $donorsCount;
            });
        } catch (\Exception $e) {
            Log::error('Failed to broadcast blood request', [
                'blood_request_id' => $bloodRequest->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Check if blood request has valid location data
     *
     * @param BloodRequest $bloodRequest
     * @return bool
     */
    private function hasValidLocation(BloodRequest $bloodRequest): bool
    {
        return !is_null($bloodRequest->lat)
            && !is_null($bloodRequest->lng)
            && !empty($bloodRequest->search_radius_km);
    }

    /**
     * Find eligible donors for a blood request
     *
     * @param BloodRequest $bloodRequest
     * @return Collection
     */
    private function findEligibleDonors(BloodRequest $bloodRequest): Collection
    {
        $compatibleBloodTypes = $bloodRequest->blood_type->getCompatibleDonorTypes();

        // Urgency-based logic: Critical requests get expanded radius and reduced cooldown
        $isCritical = $bloodRequest->urgency_level === \App\Enums\UrgencyLevel::CRITICAL;

        $searchRadius = $isCritical
            ? $bloodRequest->search_radius_km * 3  // Triple radius for critical
            : $bloodRequest->search_radius_km;

        $notificationCooldownHours = $isCritical
            ? 0.5  // 30 minutes for critical (allows re-notification faster)
            : 2;   // 2 hours for normal/low urgency

        return Donor::withinRadius(
            $bloodRequest->lat,
            $bloodRequest->lng,
            $searchRadius,
            $bloodRequest->organization->governorate_id
        )
            ->whereHas('healthProfile', function ($query) use ($compatibleBloodTypes) {
                $query->where(function ($q) use ($compatibleBloodTypes) {
                    $q->whereIn('verified_blood_type', $compatibleBloodTypes)
                        ->orWhere(function ($q2) use ($compatibleBloodTypes) {
                            $q2->whereNull('verified_blood_type')
                                ->whereIn('blood_type', $compatibleBloodTypes);
                        });
                })
                    ->where('is_eligible', '=', true, 'and')
                    ->where(function ($q) {
                        $q->whereNull('next_eligible_date')
                            ->orWhereDate('next_eligible_date', '<=', now());
                    });
            })
            ->whereDoesntHave('eligibilityLogs', function ($query) {
                $query->where('is_eligible', '=', false, 'and')
                    ->where('is_permanent', '=', true, 'and');
            })
            // Time-based notification de-duplication: urgency-based cooldown
            // Critical: 30 min, Normal/Low: 2 hours
            ->whereDoesntHave('responses', function ($query) use ($notificationCooldownHours) {
                $query->where('created_at', '>=', now()->subHours($notificationCooldownHours))
                    ->whereIn('status', [
                        \App\Enums\RequestResponseStatus::PENDING,
                        \App\Enums\RequestResponseStatus::ACCEPTED
                    ]);
            })
            ->get();
    }

    /**
     * Update blood request status to broadcasted
     *
     * @param BloodRequest $bloodRequest
     * @return void
     */
    private function updateBroadcastStatus(BloodRequest $bloodRequest): void
    {
        $bloodRequest->status = \App\Enums\BloodRequestStatus::BROADCASTED;
        $bloodRequest->broadcasted_at = now();
        $bloodRequest->save();
    }
}
