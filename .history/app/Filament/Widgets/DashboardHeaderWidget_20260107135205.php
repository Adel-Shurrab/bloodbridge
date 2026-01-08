<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class DashboardHeaderWidget extends Widget
{
    protected string $view = 'filament.widgets.dashboard-header-widget';

    protected int | string | array $columnSpan = 'full';

    public function getUser()
    {
        return Auth::user();
    }
}
