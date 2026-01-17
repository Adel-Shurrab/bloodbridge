<?php

namespace App\Filament\Organization\Resources\BloodRequests;

use App\Filament\Organization\Resources\BloodRequests\Pages\CreateBloodRequest;
use App\Filament\Organization\Resources\BloodRequests\Pages\EditBloodRequest;
use App\Filament\Organization\Resources\BloodRequests\Pages\ListBloodRequests;
use App\Filament\Organization\Resources\BloodRequests\Pages\ViewBloodRequest;
use App\Filament\Organization\Resources\BloodRequests\Schemas\BloodRequestForm;
use App\Filament\Organization\Resources\BloodRequests\Schemas\BloodRequestInfolist;
use App\Filament\Organization\Resources\BloodRequests\Tables\BloodRequestsTable;
use App\Models\BloodRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BloodRequestResource extends Resource
{
    protected static ?string $model = BloodRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'blood_type';

    public static function form(Schema $schema): Schema
    {
        return BloodRequestForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BloodRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BloodRequestsTable::configure($table);
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
            'index' => ListBloodRequests::route('/'),
            'create' => CreateBloodRequest::route('/create'),
            'view' => ViewBloodRequest::route('/{record}'),
            'edit' => EditBloodRequest::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
