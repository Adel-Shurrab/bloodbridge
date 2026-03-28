<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Enums\RequestResponseStatus;
use App\Enums\UrgencyLevel;
use Illuminate\Support\Facades\DB;

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
        DB::table('request_responses')
            ->join('blood_requests', 'blood_requests.id', '=', 'request_responses.blood_request_id')
            ->where('request_responses.status', RequestResponseStatus::PENDING)
            ->where('blood_requests.urgency_level', UrgencyLevel::CRITICAL)
            ->where('request_responses.responded_at', '<=', now()->subHours(8))
            ->update(['request_responses.status' => RequestResponseStatus::UNREACHABLE]);

        DB::table('request_responses')
            ->join('blood_requests', 'blood_requests.id', '=', 'request_responses.blood_request_id')
            ->where('request_responses.status', RequestResponseStatus::PENDING)
            ->where('blood_requests.urgency_level', UrgencyLevel::NORMAL)
            ->where('request_responses.responded_at', '<=', now()->subHours(48))
            ->update(['request_responses.status' => RequestResponseStatus::UNREACHABLE]);
    }
}
