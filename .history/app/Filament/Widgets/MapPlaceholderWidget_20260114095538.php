<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class MapPlaceholderWidget extends Widget
{
    protected string $view = 'filament.widgets.map-placeholder-widget';

    protected int | string | array $columnSpan = 'full';
}
