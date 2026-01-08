<?php

namespace App\Filament\Widgets;

use App\Models\BloodRequest;
use App\Models\Donor;
use App\Models\Organization;
use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('عمليات التحقق المعلقة', Organization::where('approval_status', Organization::STATUS_PENDING)->count())
                ->description('بانتظار مراجعة المسؤول')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('إجمالي المتبرعين', Donor::count())
                ->description('المسجلين في النظام')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
            Stat::make('المؤسسات المعتمدة', Organization::where('approval_status', Organization::STATUS_APPROVED)->count())
                ->description('نشطة في النظام')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
            Stat::make('التبرعات المكتملة', BloodRequest::where('status', BloodRequest::STATUS_FULFILLED)->count())
                ->description('عمليات ناجحة تم توثيقها')
                ->descriptionIcon('heroicon-m-heart')
                ->color('primary'),
        ];
    }
}
