<?php

namespace App\Filament\Admin\Resources\Donors;

use App\Filament\Admin\Resources\Donors\Pages\CreateDonor;
use App\Filament\Admin\Resources\Donors\Pages\EditDonor;
use App\Filament\Admin\Resources\Donors\Pages\ListDonors;
use App\Filament\Admin\Resources\Donors\Pages\ViewDonor;
use App\Filament\Admin\Resources\Donors\Schemas\DonorForm;
use App\Filament\Admin\Resources\Donors\Schemas\DonorInfolist;
use App\Filament\Admin\Resources\Donors\Tables\DonorsTable;
use App\Filament\Admin\Resources\Donors\RelationManagers\ResponsesRelationManager;
use App\Models\Donor;
use BackedEnum;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DonorResource extends Resource
{
    protected static ?string $model = Donor::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-heart';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.donors.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.operations');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.donors.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.donors.plural');
    }


    protected static ?string $recordTitleAttribute = 'name';



    public static function form(Schema $schema): Schema
    {
        return DonorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DonorsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DonorInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [
            ResponsesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListDonors::route('/'),
            'create' => CreateDonor::route('/create'),
            'view'   => ViewDonor::route('/{record}'),
            'edit'   => EditDonor::route('/{record}/edit'),
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
