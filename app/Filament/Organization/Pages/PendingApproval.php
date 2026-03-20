<?php

namespace App\Filament\Organization\Pages;

use App\Enums\OrganizationStatus;
use Filament\Facades\Filament;
use Filament\Pages\Page;


class PendingApproval extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clock';

    protected string $view = 'filament.organization.pages.pending-approval';

    protected static bool $shouldRegisterNavigation = false;

    public function getTitle(): string
    {
        $tenant = Filament::getTenant();

        if ($tenant && $tenant->approval_status === OrganizationStatus::REJECTED) {
            return __('Organization Request Rejected');
        }

        return __('Pending Approval');
    }

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    public function getHeading(): string
    {
        return $this->getTitle();
    }

    public static function canAccess(): bool
    {
        
        $tenant = Filament::getTenant();
        return $tenant && $tenant->approval_status !== OrganizationStatus::APPROVED;
    }
}

