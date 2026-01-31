<?php

namespace App\Filament\Organization\Widgets\Statistics;

use App\Models\BloodRequest;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class BloodRequestTrendWidget extends ChartWidget
{
    protected ?string $heading = 'طلبات الدم - آخر 30 يوم';

    protected ?string $maxHeight = '300px';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $organization = Auth::user()->organization;

        $data = [];
        $labels = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = BloodRequest::where('organization_id', $organization->id)
                ->whereDate('created_at', $date)
                ->count();

            $data[] = $count;
            $labels[] = $date->format('M d');
        }

        return [
            'datasets' => [
                [
                    'label' => 'طلبات الدم',
                    'data' => $data,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
