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
        return __('donor.medical_exclusion');
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
            'organization_name' => $log?->organization?->localized_org_name ?? __('donor.specialized_medical_facility'),
            'reason' => $log?->rejection_reason ?? __('donor.medical_reasons_preventing_permanent_donation'),
            'date' => $log?->created_at?->format('Y/m/d') ?? now()->format('Y/m/d'),
        ];
    }
}
