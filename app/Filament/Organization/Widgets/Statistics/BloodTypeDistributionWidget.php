<?php

namespace App\Filament\Organization\Widgets\Statistics;

use App\Enums\BloodType;
use App\Models\BloodRequest;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class BloodTypeDistributionWidget extends ChartWidget
{
    protected ?string $heading = 'التوزيع حسب فصيلة الدم';

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // Cache organization ID for this request using once() to avoid repeated DB queries
        $organizationId = once(fn() => Auth::user()->organization->id);

        // Single aggregated query with GROUP BY instead of N+1 queries
        $stats = BloodRequest::query()
            ->where('organization_id', $organizationId)
            ->selectRaw('blood_type, count(*) as total')
            ->groupBy('blood_type')
            ->pluck('total', 'blood_type')
            ->toArray();

        $data = [];
        $labels = [];
        $colors = [];

        $colorMap = [
            'O_POSITIVE' => '#ef4444',
            'O_NEGATIVE' => '#dc2626',
            'A_POSITIVE' => '#f97316',
            'A_NEGATIVE' => '#ea580c',
            'B_POSITIVE' => '#06b6d4',
            'B_NEGATIVE' => '#0891b2',
            'AB_POSITIVE' => '#8b5cf6',
            'AB_NEGATIVE' => '#7c3aed',
            'UNKNOWN' => '#6b7280',
        ];

        // Process in memory instead of database lookups
        foreach (BloodType::cases() as $type) {
            $count = $stats[$type->value] ?? 0;

            if ($count > 0) {
                $data[] = $count;
                $labels[] = $type->getLabel();
                $colors[] = $colorMap[$type->name] ?? '#6b7280';
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'الطلبات',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderWidth' => 0,
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
