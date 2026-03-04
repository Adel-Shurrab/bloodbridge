<?php

namespace App\Filament\Donor\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

class DonorStatsOverview extends Widget
{
    protected string $view = 'filament.widgets.stats-overview';

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        /** @var User $user */
        $user = Auth::user();
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
                value: $profile->next_eligible_date instanceof Carbon
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
            
        ];
    }

    protected function makeStat(string $label, $value, string $icon, string $theme): array
    {
        $themes = [
            'red'     => [
                'color' => '#d32f2f',
                'bg' => '#fee2e2',
                'bg_gradient' => 'linear-gradient(135deg, #fee2e2, #fecaca)',
                'border' => 'rgba(211, 47, 47, 0.2)'
            ],
            'blue'    => [
                'color' => '#3b82f6',
                'bg' => '#dbeafe',
                'bg_gradient' => 'linear-gradient(135deg, #dbeafe, #bfdbfe)',
                'border' => 'rgba(59, 130, 246, 0.2)'
            ],
            'emerald' => [
                'color' => '#10b981',
                'bg' => '#d1fae5',
                'bg_gradient' => 'linear-gradient(135deg, #d1fae5, #a7f3d0)',
                'border' => 'rgba(16, 185, 129, 0.2)'
            ],
            'orange'  => [
                'color' => '#f97316',
                'bg' => '#fff7ed',
                'bg_gradient' => 'linear-gradient(135deg, #fff7ed, #ffedd5)',
                'border' => 'rgba(249, 115, 22, 0.2)'
            ],
        ];

        $selected = $themes[$theme] ?? $themes['blue'];

        return [
            'label' => $label,
            'value' => $value,
            'icon'  => $icon,
            'color' => $selected['color'],
            'bg'    => $selected['bg'],
            'bg_gradient' => $selected['bg_gradient'],
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
