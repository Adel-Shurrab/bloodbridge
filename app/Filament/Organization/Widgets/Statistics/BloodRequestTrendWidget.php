<?php

namespace App\Filament\Organization\Widgets\Statistics;

use App\Models\Organization;
use App\Models\BloodRequest;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\Auth;

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
        $organizationId = $this->getOrganizationId();

        if (! $organizationId) {
            return [
                'datasets' => [
                    [
                        'label' => __('Blood Requests'),
                        'data' => [],
                        'borderColor' => '#3b82f6',
                        'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                        'fill' => true,
                    ],
                ],
                'labels' => [],
            ];
        }

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
