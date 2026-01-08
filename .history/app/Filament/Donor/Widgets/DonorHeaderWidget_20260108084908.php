<?php

namespace App\Filament\Donor\Widgets;

use Filament\Widgets\Widget;

class DonorHeaderWidget extends Widget
{
    protected string $view = 'filament.donor.widgets.donor-header-widget';

    protected int | string | array $columnSpan = 'full';
}
