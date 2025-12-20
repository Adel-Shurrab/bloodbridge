<?php

namespace App\Filament\Donor\Resources\Bloodrequests;

use App\Filament\Donor\Resources\Bloodrequests\Pages\CreateBloodrequest;
use App\Filament\Donor\Resources\Bloodrequests\Pages\EditBloodrequest;
use App\Filament\Donor\Resources\Bloodrequests\Pages\ListBloodrequests;
use App\Filament\Donor\Resources\Bloodrequests\Schemas\BloodrequestForm;
use App\Filament\Donor\Resources\Bloodrequests\Tables\BloodrequestsTable;
use App\Models\Bloodrequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BloodrequestResource extends Resource
{
    protected static ?string $model = Bloodrequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return BloodrequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BloodrequestsTable::configure($table);
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
            'index' => ListBloodrequests::route('/'),
            'create' => CreateBloodrequest::route('/create'),
            'edit' => EditBloodrequest::route('/{record}/edit'),
        ];
    }
}
