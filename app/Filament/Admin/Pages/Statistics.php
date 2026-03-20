<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\AdvancedStatsOverview;
use App\Filament\Admin\Widgets\BloodTypeDemandWidget;
use App\Filament\Admin\Widgets\EngagementChartWidget;
use App\Filament\Admin\Widgets\RecentActivityWidget;
use App\Filament\Admin\Widgets\StatsOverview;
use Filament\Pages\Page;

class Statistics extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('filament.pages.statistics.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.system-reports');
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return __('filament.pages.statistics.title');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AdvancedStatsOverview::class,
            BloodTypeDemandWidget::class,
            RecentActivityWidget::class,
            EngagementChartWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }
}
