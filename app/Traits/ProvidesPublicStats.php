<?php

namespace App\Traits;

use App\Models\Donor;
use App\Models\Appointment;
use App\Models\Organization;
use App\Enums\AppointmentStatus;
use App\Enums\OrganizationStatus;
use Illuminate\Support\Facades\Cache;

trait ProvidesPublicStats
{
    /**
     * Calculate and cache public statistics.
     * Caches results for 1 hour to prevent database load.
     *
     * @return array
     */
    protected function getStats(): array
    {
        return Cache::remember('public_stats', 60 * 60, function () {
            $donationsCount = Appointment::where('status', '=', AppointmentStatus::COMPLETED, 'and')->count('*');

            $orgsCount = Organization::where('approval_status', '=', OrganizationStatus::APPROVED, 'and')->count('*');

            $donorsCount = Donor::count('*');

            $livesSaved = $donationsCount * 3;

            return [
                'donations_count' => $donationsCount,
                'orgs_count' => $orgsCount,
                'donors_count' => $donorsCount,
                'lives_saved' => $livesSaved,
            ];
        });
    }
}
