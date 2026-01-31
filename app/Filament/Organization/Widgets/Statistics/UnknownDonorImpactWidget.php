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

        // UNKNOWN donors registered
        $unknownDonors = Donor::whereHas('healthProfile', function ($query) {
            $query->where('blood_type', BloodType::UNKNOWN)
                ->whereNull('verified_blood_type');
        })
            ->count();

        // UNKNOWN donors who got verified
        $verifiedUnknown = Donor::whereHas('healthProfile', function ($query) {
            $query->where('blood_type', BloodType::UNKNOWN)
                ->whereNotNull('verified_blood_type');
        })
            ->count();

        // Conversion rate
        $totalUnknown = $unknownDonors + $verifiedUnknown;
        $conversionRate = $totalUnknown > 0 ? round(($verifiedUnknown / $totalUnknown) * 100, 1) : 0;

        // UNKNOWN donors who responded to this org
        $unknownResponses = RequestResponse::whereHas('bloodRequest', function ($query) use ($organization) {
            $query->where('organization_id', $organization->id);
        })
            ->whereHas('donor.healthProfile', function ($query) {
                $query->where('blood_type', BloodType::UNKNOWN);
            })
            ->count();

        return [
            Stat::make('🇵🇸 متبرعون بفصيلة غير معروفة', number_format($unknownDonors))
                ->description('Gaza Innovation - توسيع قاعدة المتبرعين')
                ->descriptionIcon('heroicon-o-user-plus')
                ->color('gray'),

            Stat::make('معدل التحقق', $conversionRate . '%')
                ->description('تم التحقق من فصيلتهم')
                ->descriptionIcon('heroicon-o-shield-check')
                ->color($conversionRate > 50 ? 'success' : 'warning'),

            Stat::make('استجابات المتبرعين المجهولين', number_format($unknownResponses))
                ->description('ساهموا في الطلبات')
                ->descriptionIcon('heroicon-o-heart')
                ->color('info'),
        ];
    }
}
