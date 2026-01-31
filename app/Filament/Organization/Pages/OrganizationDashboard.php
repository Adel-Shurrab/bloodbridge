<?php

namespace App\Filament\Organization\Pages;

use App\Filament\Organization\Widgets\BloodRequestStatsWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class OrganizationDashboard extends BaseDashboard
{
    protected static ?string $title = 'لوحة التحكم';

    public function getWidgets(): array
    {
        return [
            \App\Filament\Organization\Widgets\OrganizationHeaderWidget::class,
            BloodRequestStatsWidget::class,
        ];
    }
}
