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

    protected static string|\UnitEnum|null $navigationGroup = 'الإحصائيات';

    protected string $view = 'filament.pages.statistics';

    protected static ?string $title = 'الإحصائيات';

    protected static ?string $navigationLabel = 'الإحصائيات';

    protected function getHeaderWidgets(): array
    {
        return [
            //
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            //
        ];
    }
}
