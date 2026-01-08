<?php

namespace App\Filament\Widgets;

use App\Models\BloodRequest;
use App\Models\Donor;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class BloodTypeDemandWidget extends Widget
{
    protected string $view = 'filament.widgets.blood-type-demand-widget';

    protected int | string | array $columnSpan = 1;

    public function getDemandData(): array
    {
        $stats = BloodRequest::select('blood_type', DB::raw('count(*) as count'))
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

        $bloodTypeOptions = Donor::getBloodTypeOptions();

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
