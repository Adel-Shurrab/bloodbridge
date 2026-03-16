<?php

namespace App\Filament\Donor\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use LaraZeus\SpatieTranslatable\Resources\Concerns\HasActiveLocaleSwitcher;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;

class IneligibleDonor extends Page
{
    use HasActiveLocaleSwitcher;

    public ?string $activeLocale = null;
    protected string $view = 'filament.donor.pages.ineligible-donor';

    protected static bool $shouldRegisterNavigation = false;

    public function getTitle(): string
    {
        return __('Medical Exclusion');
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

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
            'organization_name' => $log?->organization?->org_name ?? __('Specialized Medical Facility'),
            'reason' => $log?->rejection_reason ?? __('Medical reasons preventing permanent blood donation'),
            'date' => $log?->created_at?->format('Y/m/d') ?? now()->format('Y/m/d'),
        ];
    }
}

