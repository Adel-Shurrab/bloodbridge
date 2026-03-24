<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;

class Statistics extends Page
{
    protected string $view = 'filament.pages.statistics';

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
}
