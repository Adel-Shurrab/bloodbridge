<?php

namespace App\Filament\Donor\Widgets;

use App\Models\Donor;
use App\Models\DonorHealthProfile;
use Filament\Widgets\Widget;

class DonorStatsOverview extends Widget
{
    protected string $view = 'filament.widgets.stats-overview';

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $donor = $user->donor;
        $profile = $donor?->healthProfile;

        if (!$profile) {
            return [];
        }

        return [
            $this->makeStat(
                label: 'إجمالي التبرعات',
                value: $profile->total_donations ?? 0,
                icon: 'heroicon-s-heart',
                theme: 'red',
            ),
            $this->makeStat(
                label: 'موعد التبرع القادم',
                value: $profile->next_eligible_date instanceof \Carbon\Carbon
                    ? $profile->next_eligible_date->format('Y/m/d')
                    : 'مؤهل الآن',
                icon: 'heroicon-s-calendar',
                theme: 'blue',
            ),
            $this->makeStat(
                label: 'حالة الأهلية',
                value: $profile->is_eligible ? 'مؤهل' : 'غير مؤهل حالياً',
                icon: $profile->is_eligible ? 'heroicon-s-check-circle' : 'heroicon-s-x-circle',
                theme: $profile->is_eligible ? 'emerald' : 'orange',
            ),
            // You can add more donor-specific stats here
        ];
    }

    protected function makeStat(string $label, $value, string $icon, string $theme): array
    {
        $themes = [
            'red'     => ['color' => '#ef4444', 'bg' => '#fff5f5', 'border' => 'rgba(239, 68, 68, 0.2)'],
            'blue'    => ['color' => '#2563eb', 'bg' => '#eff6ff', 'border' => 'rgba(37, 99, 235, 0.2)'],
            'emerald' => ['color' => '#10b981', 'bg' => '#ecfdf5', 'border' => 'rgba(16, 185, 129, 0.2)'],
            'orange'  => ['color' => '#f97316', 'bg' => '#fff7ed', 'border' => 'rgba(249, 115, 22, 0.2)'],
        ];

        $selected = $themes[$theme] ?? $themes['blue'];

        return [
            'label' => $label,
            'value' => $value,
            'icon'  => $icon,
            'color' => $selected['color'],
            'bg'    => $selected['bg'],
            'border' => $selected['border'],
        ];
    }

    protected function getViewData(): array
    {
        return [
            'stats' => $this->getStats(),
        ];
    }
}
