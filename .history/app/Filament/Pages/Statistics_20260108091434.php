<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\StatsOverview;
use Filament\Pages\Page;

class Statistics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.statistics';

    protected static ?string $title = 'الإحصائيات';

    protected static ?string $navigationLabel = 'الإحصائيات';

    protected function getHeaderWidgets(): array
    {
        return [
            // We can add widgets here or directly in the blade
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            //
        ];
    }
}
