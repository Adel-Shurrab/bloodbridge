<?php

namespace App\Filament\Admin\Resources\Achievements\Pages;

use App\Filament\Admin\Resources\Achievements\AchievementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use LaraZeus\SpatieTranslatable\Resources\Pages\ListRecords\Concerns\Translatable;

class ListAchievements extends ListRecords
{
    use Translatable;
    protected static string $resource = AchievementResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
