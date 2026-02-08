<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\BloodRequest;
use App\Models\User;
use App\Notifications\BloodRequestMatchNotification;

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
        $bloodRequest = BloodRequest::find($this->bloodRequestId);

        if (!$bloodRequest) {
            return;
        }

        $userIds = array_keys($this->donorData);

        // Eager load healthProfile to prevent N+1 queries (used in notification)
        /** @var \Illuminate\Support\Collection<int, User> $users */
        User::with('healthProfile')
            ->whereIn('id', $userIds)
            ->chunk(10, function (\Illuminate\Support\Collection $users) use ($bloodRequest) {
                foreach ($users as $user) {
                    $distance = $this->donorData[$user->id] ?? null;
                    $user->notify(new BloodRequestMatchNotification($bloodRequest, $distance));
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
