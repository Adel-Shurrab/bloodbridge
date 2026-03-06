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
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
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

    protected static string|\UnitEnum|null $navigationGroup = 'التواصل';

    protected static ?string $navigationLabel = 'رسائل التواصل';

    protected static ?string $modelLabel = 'رسالة تواصل';

    protected static ?string $pluralModelLabel = 'رسائل التواصل';

    protected static ?string $recordTitleAttribute = 'subject';

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
