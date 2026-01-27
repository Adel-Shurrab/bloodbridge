<?php

namespace App\Filament\Admin\Widgets;

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
        $stats = BloodRequest::query()
            ->select('blood_type', DB::raw('count(*) as count'))
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

        $statsFirst = $stats->first();
        $mostNeeded = $statsFirst
            ? ($statsFirst->blood_type instanceof \App\Enums\BloodType
                ? $statsFirst->blood_type->getLabel()
                : \App\Enums\BloodType::from($statsFirst->blood_type)->getLabel())
            : 'N/A';

        return [
            'most_needed' => $mostNeeded,
            'breakdown' => $stats->take(4)->map(fn($stat) => [
                'label' => $stat->blood_type instanceof \App\Enums\BloodType
                    ? $stat->blood_type->getLabel()
                    : \App\Enums\BloodType::from($stat->blood_type)->getLabel(),
                'value' => round(($stat->count / $totalCount) * 100) . '%',
            ])->toArray(),
        ];
    }
}
