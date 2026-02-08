<?php

namespace App\Filament\Organization\Widgets\Statistics;

use App\Models\BloodRequest;
use App\Models\RequestResponse;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class OverviewStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Cache organization for this request to avoid multiple DB queries
        $organization = once(fn() => Auth::user()->organization);

        // Total Requests (all time)
        $totalRequests = BloodRequest::where('organization_id', $organization->id)->count();

        // Total Completed Donations
        $totalDonations = RequestResponse::whereHas('bloodRequest', function ($query) use ($organization) {
            $query->where('organization_id', $organization->id);
        })
            ->where('status', \App\Enums\RequestResponseStatus::COMPLETED)
            ->count();

        // Active Donors (responded in last 30 days)
        $activeDonors = RequestResponse::whereHas('bloodRequest', function ($query) use ($organization) {
            $query->where('organization_id', $organization->id);
        })
            ->where('created_at', '>=', now()->subDays(30))
            ->distinct('donor_id')
            ->count('donor_id');

        // Response Rate
        $totalResponses = RequestResponse::whereHas('bloodRequest', function ($query) use ($organization) {
            $query->where('organization_id', $organization->id);
        })->count();

        $responseRate = $totalRequests > 0 ? round(($totalResponses / $totalRequests) * 100, 1) : 0;

        return [
            Stat::make('إجمالي الطلبات', number_format($totalRequests))
                ->description('جميع طلبات الدم')
                ->descriptionIcon('heroicon-o-clipboard-document-list')
                ->color('primary')
                ->chart($this->getRequestsTrend()),

            Stat::make('التبرعات المكتملة', number_format($totalDonations))
                ->description('إجمالي التبرعات الناجحة')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('المتبرعون النشطون', number_format($activeDonors))
                ->description('آخر 30 يوم')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('info'),

            Stat::make('معدل الاستجابة', $responseRate . '%')
                ->description('نسبة الحصول على ردود')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color($responseRate > 70 ? 'success' : ($responseRate > 40 ? 'warning' : 'danger')),
        ];
    }

    private function getRequestsTrend(): array
    {
        // Cache organization ID for this request
        $organizationId = once(fn() => Auth::user()->organization->id);

        // Use Trend library instead of loop - single aggregated query (7 queries -> 1)
        $data = Trend::query(
            BloodRequest::query()->where('organization_id', $organizationId)
        )
            ->between(
                start: now()->subDays(6)->startOfDay(),
                end: now(),
            )
            ->perDay()
            ->count();

        return $data->map(fn(TrendValue $value) => $value->aggregate)->toArray();
    }
}
