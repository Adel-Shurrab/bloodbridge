<?php

namespace App\Filament\Organization\Widgets;

use App\Models\Organization;
use Filament\Widgets\Widget;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class OrganizationHeaderWidget extends Widget
{
    protected string $view = 'filament.organization.widgets.organization-header-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = -1; 

    public function getUser(): ?Authenticatable
    {
        return Auth::user();
    }

    public function getOrganizationRecord(): ?Organization
    {
        $tenant = filament()->getTenant();

        if ($tenant instanceof Organization) {
            return $tenant;
        }

        return Auth::user()?->organization;
    }
}
