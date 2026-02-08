<?php

namespace App\Filament\Organization\Widgets\Statistics;

use App\Models\BloodRequest;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class BloodRequestTrendWidget extends ChartWidget
{
    protected ?string $heading = 'طلبات الدم - آخر 30 يوم';

    protected ?string $maxHeight = '300px';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // Cache organization ID for this request using once() to avoid repeated DB queries
        $organizationId = once(fn() => Auth::user()->organization->id);

        // Use Trend library to replace loop - single aggregated query
        $data = Trend::query(
            BloodRequest::query()->where('organization_id', $organizationId)
        )
            ->between(
                start: now()->subDays(29),
                end: now(),
            )
            ->perDay()
            ->count();

        // Format data for chart
        return [
            'datasets' => [
                [
                    'label' => 'طلبات الدم',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
