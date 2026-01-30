<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RequestResponse;
use App\Enums\RequestResponseStatus;
use App\Enums\UrgencyLevel;
use Carbon\Carbon;

class CleanupStaleResponses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blood:cleanup-stale-responses {--hours=6 : Hours after which PENDING responses are marked as IGNORED}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically mark stale PENDING responses as IGNORED after specified timeout';

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
