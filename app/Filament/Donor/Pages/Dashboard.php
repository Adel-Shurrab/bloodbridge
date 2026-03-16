<?php

namespace App\Filament\Donor\Pages;
use \App\Filament\Donor\Widgets\DonorStatsOverviewWidget;
use \App\Filament\Donor\Widgets\DonorHeaderWidget;

class Dashboard extends \Filament\Pages\Dashboard
{
    public function getTitle(): string
    {
        return __('filament.pages.dashboard.title');
    }

    public function getHeading(): string
    {
        return '';
    }
    public function getWidgets(): array
    {
        return [
            DonorHeaderWidget::class,
            DonorStatsOverviewWidget::class,
        ];
    }

    public function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Donor\Widgets\EligibilityCountdownWidget::class,
        ];
    }

}

