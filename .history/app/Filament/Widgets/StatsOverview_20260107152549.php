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
            [
                'label' => 'عمليات التحقق المعلقة',
                'value' => Organization::where('approval_status', Organization::STATUS_PENDING)->count(),
                'icon' => 'heroicon-s-clock',
                'color' => 'text-red-500',
                'bg' => 'bg-red-50',
                'shadow' => 'shadow-red-50',
                'border' => 'border-red-50',
                'desc' => 'بانتظار مراجعة المسؤول',
            ],
            [
                'label' => 'إجمالي المتبرعين',
                'value' => number_format(Donor::count()),
                'icon' => 'heroicon-s-users',
                'color' => 'text-blue-600',
                'bg' => 'bg-blue-50',
                'shadow' => 'shadow-blue-50',
                'border' => 'border-blue-50',
                'desc' => 'المسجلين في النظام',
            ],
            [
                'label' => 'المؤسسات المعتمدة',
                'value' => Organization::where('approval_status', Organization::STATUS_APPROVED)->count(),
                'icon' => 'heroicon-s-check-badge',
                'color' => 'text-emerald-500',
                'bg' => 'bg-emerald-50',
                'shadow' => 'shadow-emerald-50',
                'border' => 'border-emerald-50',
                'desc' => 'نشطة في النظام',
            ],
            [
                'label' => 'التبرعات المكتملة',
                'value' => BloodRequest::where('status', BloodRequest::STATUS_FULFILLED)->count(),
                'icon' => 'heroicon-s-heart',
                'color' => 'text-pink-500',
                'bg' => 'bg-pink-50',
                'shadow' => 'shadow-pink-50',
                'border' => 'border-pink-50',
                'desc' => 'عمليات ناجحة تم توثيقها',
            ],
        ];
    }

    protected function getViewData(): array
    {
        return [
            'stats' => $this->getStats(),
        ];
    }
}
