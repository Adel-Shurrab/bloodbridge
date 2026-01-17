<?php

namespace App\Filament\Organization\Resources\BloodRequests\Pages;

use App\Filament\Organization\Resources\BloodRequests\BloodRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditBloodRequest extends EditRecord
{
    protected static string $resource = BloodRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
