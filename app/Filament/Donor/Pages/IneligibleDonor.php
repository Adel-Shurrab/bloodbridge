<?php

namespace App\Filament\Donor\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class IneligibleDonor extends Page
{
    protected string $view = 'filament.donor.pages.ineligible-donor';

    protected static bool $shouldRegisterNavigation = false;

    public function getHeading(): string
    {
        return '';
    }

    public function getIneligibilityData(): array
    {
        $donor = Auth::user()->donor;
        $healthProfile = $donor?->healthProfile;

        $log = $donor?->eligibilityLogs()
            ->where('is_permanent', true)
            ->latest()
            ->first();

        return [
            'organization_name' => $log?->organization?->org_name ?? 'مرفق طبي متخصص',
            'reason' => $log?->rejection_reason ?? 'أسباب صحية تمنع التبرع بالدم بشكل دائم',
            'date' => $log?->created_at?->format('Y/m/d') ?? now()->format('Y/m/d'),
        ];
    }
}
