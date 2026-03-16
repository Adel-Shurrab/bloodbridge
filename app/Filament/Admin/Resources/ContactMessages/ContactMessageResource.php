<?php

namespace App\Filament\Admin\Resources\ContactMessages;

use App\Filament\Admin\Resources\ContactMessages\Pages\ListContactMessages;
use App\Filament\Admin\Resources\ContactMessages\Pages\ViewContactMessage;
use App\Filament\Admin\Resources\ContactMessages\Schemas\ContactMessageForm;
use App\Filament\Admin\Resources\ContactMessages\Schemas\ContactMessageInfolist;
use App\Filament\Admin\Resources\ContactMessages\Tables\ContactMessagesTable;
use App\Models\ContactMessage;
use BackedEnum;
use Filament\Resources\Resource;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    use Translatable;


    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    protected static ?string $model = ContactMessage::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.contact-messages.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.communication');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.contact-messages.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.contact-messages.plural');
    }

    protected static ?string $recordTitleAttribute = 'subject';

    public static function getTranslatableLocales(): array
    {
        return ['ar', 'en'];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereNull('read_at')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::whereNull('read_at')->count() > 0 ? 'warning' : 'gray';
    }

    public static function form(Schema $schema): Schema
    {
        return ContactMessageForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ContactMessageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContactMessagesTable::configure($table);
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
            'index' => ListContactMessages::route('/'),
            'view' => ViewContactMessage::route('/{record}'),
        ];
    }
}
