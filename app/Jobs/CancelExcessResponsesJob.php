<?php

namespace App\Jobs;

use App\Enums\RequestResponseStatus;
use App\Models\BloodRequest;
use App\Models\RequestResponse;
use App\Notifications\ResponseNotNeededNotification;
use App\Services\QRCodeService;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Enums\BloodRequestStatus;
use App\Enums\NotificationType;

class CancelExcessResponsesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public BloodRequest $bloodRequest
    ) {}

    /**
     * Execute the job.
     */
    public function handle(QRCodeService $qrService, NotificationService $notificationService): void
    {

        $allowedStatuses = [
            BloodRequestStatus::FULFILLED,
            BloodRequestStatus::EXPIRED,
        ];

        if (! in_array($this->bloodRequest->status, $allowedStatuses, true)) {
            return;
        }

        $pendingResponses = RequestResponse::query()
            ->with('donor.user')
            ->where('blood_request_id', $this->bloodRequest->id)
            ->where('status', RequestResponseStatus::PENDING)
            ->get();

        if ($pendingResponses->isEmpty()) {
            return;
        }

        $reason = $this->bloodRequest->status === BloodRequestStatus::EXPIRED ? 'expired' : 'fulfilled';
        Log::info("Canceling {$pendingResponses->count()} pending responses for BloodRequest #{$this->bloodRequest->id} (reason: {$reason})");

        foreach ($pendingResponses as $response) {
            /** @var RequestResponse $response */
            try {

                $response->status = RequestResponseStatus::NOT_NEEDED;
                $response->save();

                $qrService->revoke($response);

                if ($response->donor && $response->donor->user) {
                    $notificationService->send(
                        $response->donor->user,
                        new ResponseNotNeededNotification($response),
                        NotificationType::RESPONSE_NOT_NEEDED
                    );
                }
            } catch (\Exception $e) {
                Log::error("Failed to cancel excess response #{$response->id}: " . $e->getMessage());
            }
        }
    }
}
