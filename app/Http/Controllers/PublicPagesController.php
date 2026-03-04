<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use App\Models\Donor;
use App\Models\Appointment;
use App\Models\Organization;

class PublicPagesController extends Controller
{
    /**
     * Show the home page with dynamic stats.
     */
    public function home(): View
    {
        $stats = $this->getStats();
        return view('pages.home', compact('stats'));
    }

    /**
     * Show the about page with dynamic stats.
     */
    public function about(): View
    {
        $stats = $this->getStats();
        return view('pages.about', compact('stats'));
    }

    /**
     * Show the contact page.
     */
    public function contact(): View
    {
        return view('pages.contact');
    }

    /**
     * Calculate and cache public statistics.
     * Caches results for 1 hour to prevent database load.
     */
    private function getStats(): array
    {
        return Cache::remember('public_stats', 60 * 60, function () {
            
            $donationsCount = Appointment::where('status', '=', \App\Enums\AppointmentStatus::COMPLETED, 'and')->count('*');

            $orgsCount = Organization::where('approval_status', '=', \App\Enums\OrganizationStatus::APPROVED, 'and')->count('*');

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
