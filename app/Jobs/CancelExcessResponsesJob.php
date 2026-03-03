<?php

namespace App\Jobs;

use App\Enums\RequestResponseStatus;
use App\Models\BloodRequest;
use App\Models\RequestResponse;
use App\Notifications\ResponseNotNeededNotification;
use App\Services\QRCodeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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
    public function handle(QRCodeService $qrService): void
    {
        // Add a small safety check to ensure it's actually fulfilled
        if ($this->bloodRequest->status !== \App\Enums\BloodRequestStatus::FULFILLED) {
            return;
        }

        // Get all pending responses for this request
        $pendingResponses = RequestResponse::query()
            ->with('donor.user') // Eager load to avoid N+1 when notifying
            ->where('blood_request_id', $this->bloodRequest->id)
            ->where('status', RequestResponseStatus::PENDING)
            ->get();

        if ($pendingResponses->isEmpty()) {
            return;
        }

        Log::info("Canceling {$pendingResponses->count()} excess responses for BloodRequest #{$this->bloodRequest->id}");

        foreach ($pendingResponses as $response) {
            /** @var RequestResponse $response */
            try {
                // 1. Update status to NOT_NEEDED
                $response->status = RequestResponseStatus::NOT_NEEDED;
                $response->save();

                // 2. Revoke their QR code
                $qrService->revoke($response);

                // 3. Notify the donor
                if ($response->donor && $response->donor->user) {
                    $response->donor->user->notify(new ResponseNotNeededNotification($response));
                }
            } catch (\Exception $e) {
                Log::error("Failed to cancel excess response #{$response->id}: " . $e->getMessage());
            }
        }
    }
}
