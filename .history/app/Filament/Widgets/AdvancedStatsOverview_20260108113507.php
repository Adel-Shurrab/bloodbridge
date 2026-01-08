<?php

namespace App\Filament\Widgets;

class AdvancedStatsOverview extends StatsOverview
{
    protected function getStats(): array
    {
        $completedRequests = \App\Models\BloodRequest::where('status', \App\Models\BloodRequest::STATUS_FULFILLED)->count();
        $totalRequests = \App\Models\BloodRequest::count();

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
                icon: 'h',
                theme: 'orange',
            ),
            $this->makeStat(
                label: 'إجمالي المتبرعين',
                value: number_format(\App\Models\Donor::count()),
                icon: 'heroicon-s-users',
                theme: 'indigo',
            ),
        ];
    }
}
