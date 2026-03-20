<?php

namespace App\Filament\Organization\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class OrganizationHeaderWidget extends Widget
{
    protected string $view = 'filament.organization.widgets.organization-header-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = -1; 

    public function getUser()
    {
        return Auth::user();
    }

    public function getOrganization()
    {
        return Auth::user()->organization;
    }
}

