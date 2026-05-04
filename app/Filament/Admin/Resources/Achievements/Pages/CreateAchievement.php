<?php

namespace App\Filament\Admin\Resources\Achievements\Pages;

use App\Filament\Admin\Resources\Achievements\AchievementResource;
use Filament\Resources\Pages\CreateRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreateAchievement extends CreateRecord
{
    use Translatable;
    protected static string $resource = AchievementResource::class;
    protected function getHeaderActions(): array { return [LocaleSwitcher::make()]; }
}
