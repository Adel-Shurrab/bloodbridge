<?php

namespace App\Filament\Admin\Widgets;

use App\Models\BloodRequest;
use Filament\Widgets\ChartWidget;

class EngagementChartWidget extends ChartWidget
{
    public function getHeading(): ?string
    {
        return __('admin.peak_engagement_times');
    }

    protected string $color = 'danger';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = [];
        $labels = [];
        $now = now();

        $results = BloodRequest::selectRaw('COUNT(*) as count, YEAR(created_at) as year, MONTH(created_at) as month')
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('year', 'month')
            ->get();

        $countsByMonth = $results->mapWithKeys(function ($result) {
            return [sprintf('%04d-%02d', $result->year, $result->month) => $result->count];
        });

        for ($i = 11; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $labels[] = $month->translatedFormat('M');
            $key = $month->format('Y-m');
            $data[] = $countsByMonth->get($key, 0);
        }

        return [
            'datasets' => [
                [
                    'label' => __('admin.blood_requests'),
                    'data' => $data,
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'borderColor' => '#ef4444',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'display' => false,
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
