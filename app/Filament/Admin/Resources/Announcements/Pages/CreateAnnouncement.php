<?php

namespace App\Filament\Admin\Resources\Announcements\Pages;

use App\Filament\Admin\Resources\Announcements\AnnouncementResource;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use Filament\Resources\Pages\CreateRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;


class CreateAnnouncement extends CreateRecord
{
    use Translatable;

    protected static string $resource = AnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }
}

