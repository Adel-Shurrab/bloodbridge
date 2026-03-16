<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets;

class Dashboard extends \Filament\Pages\Dashboard
{
    public function getHeading(): string
    {
        return '';
    }

    /**
     * Get the widgets that should be displayed on the dashboard.
     * Only showing stats cards and pending organizations.
     */
    public function getWidgets(): array
    {
        return [
            Widgets\DashboardHeaderWidget::class,
            Widgets\StatsOverview::class,
            Widgets\PendingOrganizationsWidget::class,
        ];
    }
}

