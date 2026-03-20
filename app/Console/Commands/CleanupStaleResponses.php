<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RequestResponse;
use App\Enums\RequestResponseStatus;
use App\Enums\UrgencyLevel;

class CleanupStaleResponses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blood:cleanup-stale-responses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark stale PENDING responses as UNREACHABLE (CRITICAL: 8h, NORMAL: 48h)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        RequestResponse::where('status', RequestResponseStatus::PENDING)
            ->whereHas('bloodRequest', fn($q) => $q->where('urgency_level', UrgencyLevel::CRITICAL))
            ->where('created_at', '<=', now()->subHours(8))
            ->update(['status' => RequestResponseStatus::UNREACHABLE]);

        RequestResponse::where('status', RequestResponseStatus::PENDING)
            ->whereHas('bloodRequest', fn($q) => $q->where('urgency_level', UrgencyLevel::NORMAL))
            ->where('created_at', '<=', now()->subHours(48))
            ->update(['status' => RequestResponseStatus::UNREACHABLE]);
    }
}
