<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class BloodTypeDemandWidget extends Widget
{
    protected string $view = 'filament.widgets.blood-type-demand-widget';

    protected int | string | array $columnSpan = 1;

    public function getDemandData(): array
    {
        $stats = \App\Models\BloodRequest::select('blood_type', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('blood_type')
            ->orderByDesc('count')
            ->get();

        $totalCount = $stats->sum('count');

        if ($totalCount === 0) {
            return [
                'most_needed' => 'N/A',
                'breakdown' => [],
            ];
        }

        $bloodTypeOptions = \App\Models\Donor::getBloodTypeOptions();

        $mostNeeded = $bloodTypeOptions[$stats->first()->blood_type] ?? 'Unknown';

        $breakdown = $stats->map(fn($stat) => [
            'label' => $bloodTypeOptions[$stat->blood_type] ?? $stat->blood_type,
            'value' => round(($stat->count / $totalCount) * 100) . '%',
        ])->toArray();

        return [
            'most_needed' => $mostNeeded,
            'breakdown' => array_slice($breakdown, 0, 4), // Top 4
        ];
    }
}
