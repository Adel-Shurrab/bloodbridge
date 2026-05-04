<?php

namespace App\Filament\Donor\Pages;
use \App\Filament\Donor\Widgets\DonorStatsOverviewWidget;
use \App\Filament\Donor\Widgets\DonorHeaderWidget;
use \App\Filament\Donor\Widgets\DonorLatestAchievementWidget;

class Dashboard extends \Filament\Pages\Dashboard
{
    public function getTitle(): string
    {
        return __('donor.dashboard');
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
            DonorLatestAchievementWidget::class,
        ];
    }

    public function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Donor\Widgets\EligibilityCountdownWidget::class,
        ];
    }

}
