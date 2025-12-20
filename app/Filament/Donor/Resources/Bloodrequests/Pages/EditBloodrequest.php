<?php

namespace App\Filament\Donor\Resources\Bloodrequests\Pages;

use App\Filament\Donor\Resources\Bloodrequests\BloodrequestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBloodrequest extends EditRecord
{
    protected static string $resource = BloodrequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
