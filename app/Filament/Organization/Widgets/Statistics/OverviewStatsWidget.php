<?php

namespace App\Filament\Organization\Widgets\Statistics;

use App\Models\Organization;
use App\Models\BloodRequest;
use App\Models\RequestResponse;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\Auth;

class OverviewStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $organization = $this->getOrganization();

        if (! $organization) {
            return [];
        }

        $totalRequests = BloodRequest::where('organization_id', $organization->id)->count();

        $totalDonations = RequestResponse::whereHas('bloodRequest', function ($query) use ($organization) {
            $query->where('organization_id', $organization->id);
        })
            ->where('status', \App\Enums\RequestResponseStatus::COMPLETED)
            ->count();

        $activeDonors = RequestResponse::whereHas('bloodRequest', function ($query) use ($organization) {
            $query->where('organization_id', $organization->id);
        })
            ->where('created_at', '>=', now()->subDays(30))
            ->distinct('donor_id')
            ->count('donor_id');

        $totalResponses = RequestResponse::whereHas('bloodRequest', function ($query) use ($organization) {
            $query->where('organization_id', $organization->id);
        })->count();

        $responseRate = $totalRequests > 0 ? round(($totalResponses / $totalRequests) * 100, 1) : 0;

        return [
            Stat::make(__('organization.total_requests'), number_format($totalRequests))
                ->description(__('organization.all_blood_requests'))
                ->descriptionIcon('heroicon-o-clipboard-document-list')
                ->color('primary')
                ->chart($this->getRequestsTrend()),

            Stat::make(__('organization.completed_donations'), number_format($totalDonations))
                ->description(__('organization.total_successful_donations'))
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make(__('organization.active_donors'), number_format($activeDonors))
                ->description(__('organization.last_30_days'))
                ->descriptionIcon('heroicon-o-user-group')
                ->color('info'),

            Stat::make(__('organization.response_rate'), $responseRate . '%')
                ->description(__('organization.percentage_of_responses_received'))
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color($responseRate > 70 ? 'success' : ($responseRate > 40 ? 'warning' : 'danger')),
        ];
    }

    private function getRequestsTrend(): array
    {
        $organizationId = $this->getOrganizationId();

        if (! $organizationId) {
            return [];
        }

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

    protected function getOrganization(): ?Organization
    {
        $tenant = filament()->getTenant();

        if ($tenant instanceof Organization) {
            return $tenant;
        }

        return Auth::user()?->organization;
    }

    protected function getOrganizationId(): ?int
    {
        return $this->getOrganization()?->getKey();
    }
}
