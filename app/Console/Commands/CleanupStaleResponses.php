<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RequestResponse;
use App\Enums\RequestResponseStatus;
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
        $timeoutHours = $this->option('hours');
        $cutoffTime = Carbon::now()->subHours($timeoutHours);

        $this->info("Cleaning up PENDING responses older than {$timeoutHours} hours (before {$cutoffTime})...");

        $updated = RequestResponse::where('status', RequestResponseStatus::PENDING)
            ->where('created_at', '<', $cutoffTime)
            ->update([
                'status' => RequestResponseStatus::IGNORED,
                'updated_at' => now()
            ]);

        $this->info("✓ Marked {$updated} stale PENDING responses as IGNORED.");

        return 0;
    }
}
