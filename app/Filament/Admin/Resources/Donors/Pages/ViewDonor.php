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
            Actions\EditAction::make()->label(__('admin.edit')),
            Actions\DeleteAction::make()->label(__('admin.delete')),
            Actions\RestoreAction::make()->label(__('admin.restore')),
            Actions\ForceDeleteAction::make()->label(__('admin.force_delete')),
        ];
    }
}
