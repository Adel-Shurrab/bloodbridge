<?php

namespace App\Filament\Admin\Resources\BloodRequests\Pages;

use App\Filament\Admin\Resources\BloodRequests\BloodRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use LaraZeus\SpatieTranslatable\Resources\Pages\ListRecords\Concerns\Translatable;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;


class ListBloodRequests extends ListRecords
{
    use Translatable;

    protected static string $resource = BloodRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}

