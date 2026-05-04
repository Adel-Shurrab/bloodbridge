<?php

namespace App\Filament\Admin\Resources\Achievements;

use App\Models\Achievement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

class AchievementResource extends Resource
{
    use Translatable;

    protected static ?string $model = Achievement::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-trophy';

    protected static ?int $navigationSort = 4; // after Donors(1), Orgs(2), Blood Requests(3)

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.achievements.title');
    }

    public static function getNavigationGroup(): ?string
    {
        // Same group as Donors and Organizations
        return __('filament.navigation.operations');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.achievements.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.achievements.plural');
    }

    protected static ?string $recordTitleAttribute = 'name';

    public static function getTranslatableLocales(): array
    {
        return ['ar', 'en'];
    }


    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count() ?: null;
    }

    public static function form(Schema $schema): Schema
    {
        return Schemas\AchievementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return Tables\AchievementsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAchievements::route('/'),
            'create' => Pages\CreateAchievement::route('/create'),
            'edit'   => Pages\EditAchievement::route('/{record}/edit'),
            // No 'view' page — edit page is sufficient
        ];
    }
}
