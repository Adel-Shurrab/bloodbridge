<?php

namespace App\Console\Commands;

use App\Enums\RequestResponseStatus;
use App\Enums\UrgencyLevel;
use App\Models\RequestResponse;
use App\Services\QRCodeService;
use Illuminate\Console\Command;

class CleanupStaleResponses extends Command
{
    protected $signature = 'blood:cleanup-stale-responses';
    protected $description = 'Mark stale PENDING responses as UNREACHABLE (CRITICAL: 8h, NORMAL: 48h)';

    public function handle(QRCodeService $qrService): void
    {
        $this->cleanupForUrgency(UrgencyLevel::CRITICAL, 8, $qrService);
        $this->cleanupForUrgency(UrgencyLevel::NORMAL, 48, $qrService);
    }

    private function cleanupForUrgency(UrgencyLevel $urgency, int $hours, QRCodeService $qrService): void
    {
        RequestResponse::query()
            ->whereHas('bloodRequest', fn($q) => $q->where('urgency_level', $urgency))
            ->where('status', RequestResponseStatus::PENDING)
            ->where('responded_at', '<=', now()->subHours($hours))
            ->chunkById(100, function ($responses) use ($qrService) {
                foreach ($responses as $response) {
                    $qrService->revoke($response);
                    $response->update(['status' => RequestResponseStatus::UNREACHABLE]);
                }
            });
    }
}
