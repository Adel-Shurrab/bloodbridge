<?php

namespace App\Filament\Organization\Pages;

use Filament\Pages\Page;

class Statistics extends Page
{
    protected static ?string $navigationLabel = 'الإحصائيات';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?int $navigationSort = -1;

    protected static ?string $title = 'الإحصائيات والتقارير';

    protected string $view = 'filament.organization.pages.statistics';

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Organization\Widgets\Statistics\OverviewStatsWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            \App\Filament\Organization\Widgets\Statistics\BloodRequestTrendWidget::class,
            \App\Filament\Organization\Widgets\Statistics\BloodTypeDistributionWidget::class,
            \App\Filament\Organization\Widgets\Statistics\SearchRadiusStatsWidget::class,
            \App\Filament\Organization\Widgets\Statistics\RecentResponsesWidget::class,
        ];
    }

    public function getViewData(): array
    {
        return [];
    }
}
