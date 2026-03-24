<?php

namespace App\Filament\Organization\Widgets\Statistics;

use App\Models\Organization;
use App\Models\BloodRequest;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class SearchRadiusStatsWidget extends ChartWidget
{
    protected ?string $maxHeight = '300px';

    public function getHeading(): string
    {
        return __('organization.used_search_radius');
    }

    public function getDescription(): ?string
    {
        return __('organization.distribution_of_final_search_radius');
    }

    protected function getData(): array
    {
        $organization = $this->getOrganization();

        if (! $organization) {
            return [
                'datasets' => [
                    [
                        'label' => __('organization.number_of_requests'),
                        'data' => [],
                    ],
                ],
                'labels' => [],
            ];
        }

        $radiusGroups = BloodRequest::where('organization_id', $organization->id)
            ->whereNotNull('actual_search_radius_km')
            ->selectRaw('actual_search_radius_km, COUNT(*) as count')
            ->groupBy('actual_search_radius_km')
            ->orderBy('actual_search_radius_km')
            ->get();

        $data = [];
        $labels = [];

        foreach ($radiusGroups as $group) {
            $data[] = $group->count;
            $labels[] = $group->actual_search_radius_km . ' ' . __('organization.km');
        }

        return [
            'datasets' => [
                [
                    'label' => __('organization.number_of_requests'),
                    'data' => $data,
                    'backgroundColor' => [
                        '#10b981',
                        '#3b82f6',
                        '#f59e0b',
                        '#ef4444',
                        '#8b5cf6',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOrganization(): ?Organization
    {
        $tenant = filament()->getTenant();

        if ($tenant instanceof Organization) {
            return $tenant;
        }

        return Auth::user()?->organization;
    }
}
