<?php

namespace App\Filament\Widgets;

use App\Models\BloodRequest;
use App\Models\Donor;
use App\Models\Organization;
use Filament\Widgets\Widget;

class StatsOverview extends Widget
{
    protected string $view = 'filament.widgets.stats-overview';

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        return [
            $this->makeStat(
                label: 'عمليات التحقق المعلقة',
                value: Organization::where('approval_status', Organization::STATUS_PENDING)->count(),
                icon: 'heroicon-s-document-text',
                theme: 'red',
            ),
            $this->makeStat(
                label: 'إجمالي المتبرعين',
                value: number_format(Donor::count()),
                icon: 'heroicon-s-users',
                theme: 'blue',
            ),
            $this->makeStat(
                label: 'المؤسسات المعتمدة',
                value: Organization::where('approval_status', Organization::STATUS_APPROVED)->count(),
                icon: 'heroicon-s-check',
                theme: 'emerald',
            ),
            $this->makeStat(
                label: 'التبرعات المكتملة',
                value: BloodRequest::where('status', BloodRequest::STATUS_FULFILLED)->count(),
                icon: 'heroicon-s-heart',
                theme: 'pink',
            ),
        ];
    }

    protected function makeStat(string $label, $value, string $icon, string $theme): array
    {
        $themes = [
            'red'     => [
                'color' => '#d32f2f',
                'bg_gradient' => 'linear-gradient(135deg, #fee2e2, #fecaca)',
                'border' => 'rgba(211, 47, 47, 0.2)'
            ],
            'blue'    => [
                'color' => '#3b82f6',
                'bg_gradient' => 'linear-gradient(135deg, #dbeafe, #bfdbfe)',
                'border' => 'rgba(59, 130, 246, 0.2)'
            ],
            'emerald' => [
                'color' => '#10b981',
                'bg_gradient' => 'linear-gradient(135deg, #d1fae5, #a7f3d0)',
                'border' => 'rgba(16, 185, 129, 0.2)'
            ],
            'pink'    => [
                'color' => '#ec4899',
                'bg_gradient' => 'linear-gradient(135deg, #fce7f3, #fbcfe8)',
                'border' => 'rgba(236, 72, 153, 0.2)'
            ],
            'orange'  => [
                'color' => '#f97316',
                'bg_gradient' => 'linear-gradient(135deg, #fff7ed, #ffedd5)',
                'border' => 'rgba(249, 115, 22, 0.2)'
            ],
            'indigo'  => [
                'color' => '#6366f1',
                'bg_gradient' => 'linear-gradient(135deg, #eef2ff, #e0e7ff)',
                'border' => 'rgba(99, 102, 241, 0.2)'
            ],
        ];

        $selected = $themes[$theme] ?? $themes['blue'];

        return array_merge([
            'label' => $label,
            'value' => $value,
            'icon'  => $icon,
        ], $selected);
    }

    protected function getViewData(): array
    {
        return [
            'stats' => $this->getStats(),
        ];
    }
}
