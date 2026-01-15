<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AdvancedStatsOverview;
use App\Filament\Widgets\BloodTypeDemandWidget;
use App\Filament\Widgets\EngagementChartWidget;
use App\Filament\Widgets\RecentActivityWidget;
use App\Filament\Widgets\StatsOverview;
use Filament\Pages\Page;

class Statistics extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'الإحصائيات';

    protected string $view = 'filament.pages.statistics';

    protected static ?string $title = 'الإحصائيات';

    protected static ?string $navigationLabel = 'الإحصائيات';

    protected function getHeaderWidgets(): array
    {
        return [
            AdvancedStatsOverview::class,
            EngagementChartWidget::class,
            BloodTypeDemandWidget::class,
            RecentActivityWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            //
        ];
    }
}
