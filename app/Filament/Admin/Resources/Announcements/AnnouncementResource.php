<?php

namespace App\Filament\Admin\Resources\Announcements;

use App\Filament\Admin\Resources\Announcements\Pages\CreateAnnouncement;
use App\Filament\Admin\Resources\Announcements\Pages\EditAnnouncement;
use App\Filament\Admin\Resources\Announcements\Pages\ListAnnouncements;
use App\Filament\Admin\Resources\Announcements\Pages\ViewAnnouncement;
use App\Filament\Admin\Resources\Announcements\Schemas\AnnouncementForm;
use App\Filament\Admin\Resources\Announcements\Schemas\AnnouncementInfolist;
use App\Filament\Admin\Resources\Announcements\Tables\AnnouncementsTable;
use App\Models\Announcement;
use BackedEnum;
use Filament\Resources\Resource;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AnnouncementResource extends Resource
{
    use Translatable;

    protected static ?string $model = Announcement::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.announcements.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.communication');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.announcements.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.announcements.plural');
    }

    protected static ?string $recordTitleAttribute = 'title';

    public static function getTranslatableLocales(): array
    {
        return ['ar', 'en'];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 1)->count();
    }

    public static function form(Schema $schema): Schema
    {
        return AnnouncementForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AnnouncementInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AnnouncementsTable::configure($table);
    }

    public static function canEdit(Model $record): bool
    {
        return $record->status === 0;
    }

    public static function canDelete(Model $record): bool
    {
        return $record->status === 0;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAnnouncements::route('/'),
            'create' => CreateAnnouncement::route('/create'),
            'view' => ViewAnnouncement::route('/{record}'),
            'edit' => EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
