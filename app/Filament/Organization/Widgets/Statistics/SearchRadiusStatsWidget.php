<?php

namespace App\Filament\Organization\Widgets\Statistics;

use App\Models\BloodRequest;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class SearchRadiusStatsWidget extends ChartWidget
{
    protected ?string $heading = 'Used Search Radius (Gaza Innovation)';

    protected ?string $maxHeight = '300px';

    protected ?string $description = 'Distribution of final search radius for requests';

    public function getHeading(): string
    {
        return __($this->heading);
    }

    public function getDescription(): ?string
    {
        return __($this->description);
    }

    protected function getData(): array
    {
        
        $organization = once(fn() => Auth::user()->organization);

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
            $labels[] = $group->actual_search_radius_km . __(' km');
        }

        return [
            'datasets' => [
                [
                    'label' => __('Number of Requests'),
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
}

