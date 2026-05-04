<?php

namespace App\Filament\Admin\Resources\Achievements\Pages;

use App\Filament\Admin\Resources\Achievements\AchievementResource;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

class EditAchievement extends EditRecord
{
    use Translatable;
    protected static string $resource = AchievementResource::class;
    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            // NO DeleteAction — achievements table has no deleted_at column
        ];
    }
}
