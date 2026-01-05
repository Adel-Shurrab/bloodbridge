<?php

namespace App\Filament\Resources\Organizations\Pages;

use App\Filament\Resources\Organizations\OrganizationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOrganization extends ViewRecord
{
    protected static string $resource = OrganizationResource::class;

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
