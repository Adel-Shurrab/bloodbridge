<?php

namespace App\Filament\Admin\Resources\BloodRequests\Pages;

use App\Filament\Admin\Resources\BloodRequests\BloodRequestResource;
use Filament\Actions;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;


class EditBloodRequest extends EditRecord
{
    use Translatable;

    protected static string $resource = BloodRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

