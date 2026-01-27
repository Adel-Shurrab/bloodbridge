<?php

namespace App\Filament\Admin\Resources\BloodRequests\Pages;

use App\Filament\Admin\Resources\BloodRequests\BloodRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBloodRequests extends ListRecords
{
    protected static string $resource = BloodRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
