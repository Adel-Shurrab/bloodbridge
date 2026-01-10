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
            $this->makeStat('الطلبات المكتملة', number_format($completedRequests), 'heroicon-s-check-circle', 'emerald'),
            $this->makeStat('الطلبات المرسلة', number_format($totalRequests), 'heroicon-s-play-circle', 'blue'),
            $this->makeStat('نسبة القبول', $acceptanceRate . '%', 'heroicon-s-receipt-percent', 'orange'),
            $this->makeStat('إجمالي المتبرعين', number_format(Donor::count()), 'heroicon-s-users', 'indigo'),
        ];
    }
}
