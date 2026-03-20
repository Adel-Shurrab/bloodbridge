<?php

namespace App\Filament\Organization\Widgets\Statistics;

use App\Enums\BloodType;
use App\Models\Donor;
use App\Models\RequestResponse;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class UnknownDonorImpactWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $organization = Auth::user()->organization;

        $unknownDonors = Donor::whereHas('healthProfile', function ($query) {
            $query->where('blood_type', BloodType::UNKNOWN)
                ->whereNull('verified_blood_type');
        })
            ->count();

        $verifiedUnknown = Donor::whereHas('healthProfile', function ($query) {
            $query->where('blood_type', BloodType::UNKNOWN)
                ->whereNotNull('verified_blood_type');
        })
            ->count();

        $totalUnknown = $unknownDonors + $verifiedUnknown;
        $conversionRate = $totalUnknown > 0 ? round(($verifiedUnknown / $totalUnknown) * 100, 1) : 0;

        $unknownResponses = RequestResponse::whereHas('bloodRequest', function ($query) use ($organization) {
            $query->where('organization_id', $organization->id);
        })
            ->whereHas('donor.healthProfile', function ($query) {
                $query->where('blood_type', BloodType::UNKNOWN);
            })
            ->count();

        return [
            Stat::make(__('🇵🇸 Donors with unknown blood type'), number_format($unknownDonors))
                ->description(__('Gaza Innovation - Expanding donor base'))
                ->descriptionIcon('heroicon-o-user-plus')
                ->color('gray'),

            Stat::make(__('Verification Rate'), $conversionRate . '%')
                ->description(__('Blood type verified'))
                ->descriptionIcon('heroicon-o-shield-check')
                ->color($conversionRate > 50 ? 'success' : 'warning'),

            Stat::make(__('Unknown donor responses'), number_format($unknownResponses))
                ->description(__('Contributed to requests'))
                ->descriptionIcon('heroicon-o-heart')
                ->color('info'),
        ];
    }
}

