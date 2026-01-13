<?php

namespace App\Filament\Resources\BloodRequests;

use App\Filament\Resources\BloodRequests\Pages\CreateBloodRequest;
use App\Filament\Resources\BloodRequests\Pages\EditBloodRequest;
use App\Filament\Resources\BloodRequests\Pages\ListBloodRequests;
use App\Filament\Resources\BloodRequests\Pages\ViewBloodRequest;
use App\Filament\Resources\BloodRequests\Schemas\BloodRequestForm;
use App\Filament\Resources\BloodRequests\Tables\BloodRequestsTable;
use App\Models\BloodRequest;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum as BackedEnum; // Assuming BackedEnum is UnitEnum for PHP 8.1+

class BloodRequestResource extends Resource
{
    protected static ?string $model = BloodRequest::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'المناقلات';

    protected static ?string $navigationLabel = 'طلبات الدم';

    protected static ?string $pluralModelLabel = 'طلبات الدم';

    protected static ?string $recordTitleAttribute = 'additional_notes';

    public static function form(Schema $schema): Schema
    {
        return BloodRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BloodRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBloodRequests::route('/'),
            'create' => CreateBloodRequest::route('/create'),
            'edit' => EditBloodRequest::route('/{record}/edit'),
            'view' => ViewBloodRequest::route('/{record}'),
        ];
    }
}
