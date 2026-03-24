<?php

namespace App\Filament\Organization\Pages;

use App\Filament\Organization\Widgets\BloodRequestStatsWidget;
use Filament\Pages\Dashboard as BaseDashboard;


class Dashboard extends BaseDashboard
{
    public function getTitle(): string
    {
        return __('organization.dashboard');
    }

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Organization\Widgets\OrganizationHeaderWidget::class,
            BloodRequestStatsWidget::class,
        ];
    }
}
