<?php

namespace App\Filament\Admin\Resources\Donors\Pages;

use App\Filament\Admin\Resources\Donors\DonorResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDonor extends ViewRecord
{
    protected static string $resource = DonorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->label(__('Edit')),
            Actions\DeleteAction::make()->label(__('Delete')),
            Actions\RestoreAction::make()->label(__('Restore')),
            Actions\ForceDeleteAction::make()->label(__('Force Delete')),
        ];
    }
}
