<?php

namespace App\Filament\Admin\Widgets;

use App\Models\BloodRequest;
use App\Models\Donor;

class AdvancedStatsOverview extends StatsOverview
{
    protected function getStats(): array
    {
        $completedRequests = BloodRequest::where('status', '=', \App\Enums\BloodRequestStatus::FULFILLED, 'and')->count('*');
        $totalRequests = BloodRequest::count('*');

        $acceptanceRate = $totalRequests > 0
            ? round(($completedRequests / $totalRequests) * 100, 2)
            : 0;

        return [
            $this->makeStat(__('Completed Requests'), number_format($completedRequests), 'heroicon-s-check-circle', 'emerald'),
            $this->makeStat(__('Sent Requests'), number_format($totalRequests), 'heroicon-s-play-circle', 'blue'),
            $this->makeStat(__('Acceptance Rate'), $acceptanceRate . '%', 'heroicon-s-receipt-percent', 'orange'),
            $this->makeStat(__('Total Donors'), number_format(Donor::count('*')), 'heroicon-s-users', 'indigo'),
        ];
    }
}
