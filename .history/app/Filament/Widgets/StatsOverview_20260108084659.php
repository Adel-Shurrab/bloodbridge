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
            'red'     => ['color' => '#ef4444', 'bg' => '#fff5f5', 'border' => 'rgba(239, 68, 68, 0.2)'],
            'blue'    => ['color' => '#2563eb', 'bg' => '#eff6ff', 'border' => 'rgba(37, 99, 235, 0.2)'],
            'emerald' => ['color' => '#10b981', 'bg' => '#ecfdf5', 'border' => 'rgba(16, 185, 129, 0.2)'],
            'pink'    => ['color' => '#db2777', 'bg' => '#fff1f2', 'border' => 'rgba(219, 39, 119, 0.2)'],
            'orange'  => ['color' => '#f97316', 'bg' => '#fff7ed', 'border' => 'rgba(249, 115, 22, 0.2)'],
            'indigo'  => ['color' => '#6366f1', 'bg' => '#eef2ff', 'border' => 'rgba(99, 102, 241, 0.2)'],
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
