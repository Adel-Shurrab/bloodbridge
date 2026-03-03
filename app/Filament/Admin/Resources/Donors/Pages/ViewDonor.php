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
            Actions\EditAction::make()->label('تعديل'),
            Actions\DeleteAction::make()->label('حذف'),
            Actions\RestoreAction::make()->label('استعادة'),
            Actions\ForceDeleteAction::make()->label('حذف نهائي'),
        ];
    }
}
