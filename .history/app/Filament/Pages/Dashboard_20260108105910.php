<?php

namespace App\Filament\Pages;

class Dashboard extends \Filament\Pages\Dashboard
{
    public function getHeading(): string
    {
        return '';
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\DashboardHeaderWidget::class,
            \App\Filament\Widgets\StatsOverview::class,
            \App\Filament\Widgets\PendingOrganizationsWidget::class,
        ];
    }
}
