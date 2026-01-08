<?php

namespace App\Filament\Widgets;

use App\Models\BloodRequest;
use App\Models\Donor;

class AdvancedStatsOverview extends StatsOverview
{
    protected function getStats(): array
    {
        $completedRequests = BloodRequest::where('status', BloodRequest::STATUS_FULFILLED)->count();
        $totalRequests = BloodRequest::count();

        $acceptanceRate = $totalRequests > 0
            ? round(($completedRequests / $totalRequests) * 100, 2)
            : 0;

        return [
            $this->makeStat(
                label: 'الطلبات المكتملة',
                value: number_format($completedRequests),
                icon: 'heroicon-s-check-circle',
                theme: 'emerald',
            ),
            $this->makeStat(
                label: 'الطلبات المرسلة',
                value: number_format($totalRequests),
                icon: 'heroicon-s-play-circle',
                theme: 'blue',
            ),
            $this->makeStat(
                label: 'نسبة القبول',
                value: $acceptanceRate . '%',
                icon: 'heroicon-s-receipt-percent',
                theme: 'orange',
            ),
            $this->makeStat(
                label: 'إجمالي المتبرعين',
                value: number_format(Donor::count()),
                icon: 'heroicon-s-users',
                theme: 'indigo',
            ),
        ];
    }
}
