<?php

namespace App\Console\Commands;

use App\Enums\BloodRequestStatus;
use App\Jobs\CancelExcessResponsesJob;
use App\Models\BloodRequest;
use Illuminate\Console\Command;

class ExpireOldBloodRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blood-requests:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically expire blood requests that have been active for more than 48 hours without fulfillment.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Starting to expire old blood requests...');

        $thresholdDate = now()->subHours(48);

        $requests = BloodRequest::query()
            ->whereIn('status', [
                BloodRequestStatus::PENDING,
                BloodRequestStatus::BROADCASTED,
            ])
            ->whereNull('fulfilled_at')
            ->where('created_at', '<=', $thresholdDate)
            ->get();

        $count = 0;

        foreach ($requests as $request) {
            /** @var BloodRequest $request */
            
            $request->status = BloodRequestStatus::EXPIRED;
            $request->save();

            CancelExcessResponsesJob::dispatchSync($request);

            $count++;
        }

        $this->info("Successfully expired {$count} blood request(s).");
    }
}
