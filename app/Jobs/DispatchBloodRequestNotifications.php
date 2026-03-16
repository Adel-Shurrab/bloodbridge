<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\BloodRequest;
use App\Models\RequestResponse;
use App\Models\User;
use App\Notifications\BloodRequestMatchNotification;
use App\Services\NotificationService;
use App\Enums\NotificationType;

/**
 * Production-grade job for dispatching blood request notifications to donors.
 * 
 * Features:
 * - Eager loading to prevent N+1 queries
 * - Batch processing to avoid queue payload limits
 * - Fresh data retrieval from database
 */
class DispatchBloodRequestNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Maximum batch size to prevent queue payload limits (most queues limit to 64-256KB)
     */
    const MAX_BATCH_SIZE = 100;

    /**
     * @param int $bloodRequestId
     * @param array $donorData Format: ['user_id' => distance]
     */
    public function __construct(
        public int $bloodRequestId,
        public array $donorData
    ) {}

    public function handle(): void
    {
        /** @var BloodRequest|null $bloodRequest */
        $bloodRequest = BloodRequest::query()->find($this->bloodRequestId);

        if (!$bloodRequest) {
            return;
        }

        $userIds = array_keys($this->donorData);

        /** @var \Illuminate\Support\Collection<int, User> $users */
        User::with('donor.healthProfile')
            ->whereIn('id', $userIds)
            ->chunk(10, function (\Illuminate\Support\Collection $users) use ($bloodRequest) {
                $donorIds = $users
                    ->pluck('donor.id')
                    ->filter()
                    ->values();

                $alreadyRespondedDonorIds = $donorIds->isEmpty()
                    ? collect([])
                    : RequestResponse::query()
                    ->where('blood_request_id', $bloodRequest->id)
                    ->whereIn('donor_id', $donorIds->all())
                    ->pluck('donor_id')
                    ->unique();

                foreach ($users as $user) {

                    $healthProfile = $user->donor?->healthProfile;

                    if (! $healthProfile) {
                        continue;
                    }

                    $isStillEligible = $healthProfile->is_eligible
                        && (
                            is_null($healthProfile->next_eligible_date)
                            || $healthProfile->next_eligible_date->startOfDay()->isPast()
                            || $healthProfile->next_eligible_date->startOfDay()->isToday()
                        );

                    if (! $isStillEligible) {
                        continue;
                    }

                    $donorId = $user->donor?->id;
                    if ($donorId && $alreadyRespondedDonorIds->contains($donorId)) {
                        continue;
                    }

                    $distance = $this->donorData[$user->id] ?? null;

                    // Use NotificationService for consistent error handling and logging
                    // This calls via() -> toDatabase() and toBroadcast() methods
                    app(NotificationService::class)->send(
                        $user,
                        new BloodRequestMatchNotification($bloodRequest, $distance),
                        NotificationType::BLOOD_REQUEST_MATCH
                    );
                }
            });
    }

    /**
     * Dispatch notifications in batches to avoid queue payload limits.
     * 
     * Usage: DispatchBloodRequestNotifications::dispatchBatches($bloodRequest->id, $donorData);
     */
    public static function dispatchBatches(int $bloodRequestId, array $donorData): void
    {
        $chunks = array_chunk($donorData, self::MAX_BATCH_SIZE, true);

        foreach ($chunks as $chunk) {
            self::dispatch($bloodRequestId, $chunk);
        }
    }
}
