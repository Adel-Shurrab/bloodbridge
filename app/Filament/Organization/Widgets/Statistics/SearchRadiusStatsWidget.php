<?php

namespace App\Filament\Organization\Widgets\Statistics;

use App\Models\BloodRequest;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class SearchRadiusStatsWidget extends ChartWidget
{
    protected ?string $heading = 'نطاق البحث المستخدم (Gaza Innovation)';

    protected ?string $maxHeight = '300px';

    protected ?string $description = 'توزيع نطاق البحث النهائي للطلبات';

    protected function getData(): array
    {
        // Cache organization for this request to avoid repeated DB queries
        $organization = once(fn() => Auth::user()->organization);

        // Group by actual_search_radius_km
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
            $labels[] = $group->actual_search_radius_km . ' كم';
        }

        return [
            'datasets' => [
                [
                    'label' => 'عدد الطلبات',
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
