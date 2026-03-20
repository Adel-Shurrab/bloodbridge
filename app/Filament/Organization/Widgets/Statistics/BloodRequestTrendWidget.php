<?php

namespace App\Filament\Organization\Widgets\Statistics;

use App\Models\BloodRequest;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class BloodRequestTrendWidget extends ChartWidget
{
    protected ?string $heading = 'Blood Requests - Last 30 Days';

    protected ?string $maxHeight = '300px';

    public function getHeading(): string
    {
        return __($this->heading);
    }

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        
        $organizationId = once(fn() => Auth::user()->organization->id);

        $data = Trend::query(
            BloodRequest::query()->where('organization_id', $organizationId)
        )
            ->between(
                start: now()->subDays(29),
                end: now(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => __('Blood Requests'),
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

