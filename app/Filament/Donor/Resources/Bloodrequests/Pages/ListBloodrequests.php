<?php

namespace App\Filament\Donor\Resources\Bloodrequests\Pages;

use App\Filament\Donor\Resources\Bloodrequests\BloodrequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBloodrequests extends ListRecords
{
    protected static string $resource = BloodrequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
