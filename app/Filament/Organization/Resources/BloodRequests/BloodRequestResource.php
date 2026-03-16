<?php

namespace App\Filament\Organization\Resources\BloodRequests;

use App\Filament\Organization\Resources\BloodRequests\Pages\CreateBloodRequest;
use App\Filament\Organization\Resources\BloodRequests\Pages\EditBloodRequest;
use App\Filament\Organization\Resources\BloodRequests\Pages\ListBloodRequests;
use App\Filament\Organization\Resources\BloodRequests\Schemas\BloodRequestForm;
use App\Filament\Organization\Resources\BloodRequests\Schemas\BloodRequestInfolist;
use App\Filament\Organization\Resources\BloodRequests\Tables\BloodRequestsTable;
use App\Models\BloodRequest;
use BackedEnum;
use Filament\Resources\Resource;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BloodRequestResource extends Resource
{
    use Translatable;

    protected static ?string $model = BloodRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.operations');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.blood_requests.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.blood_requests.plural');
    }

    public static function getTranslatableLocales(): array
    {
        return ['ar', 'en'];
    }

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
            RelationManagers\ResponsesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBloodRequests::route('/'),
            'create' => CreateBloodRequest::route('/create'),
            'view' => Pages\ViewBloodRequest::route('/{record}'),
            'edit' => EditBloodRequest::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $tenantId = filament()->getTenant()?->getKey();

        return parent::getEloquentQuery()
            ->when(
                $tenantId,
                fn(Builder $query): Builder => $query->where('organization_id', $tenantId),
            )
            ->withCount([
                'responses',
                'acceptedResponses as accepted_responses_count',
                'completedResponses as completed_responses_count',
            ]);
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        $tenantId = filament()->getTenant()?->getKey();

        return parent::getRecordRouteBindingEloquentQuery()
            ->when(
                $tenantId,
                fn(Builder $query): Builder => $query->where('organization_id', $tenantId),
            )
            ->withCount([
                'responses',
                'acceptedResponses as accepted_responses_count',
                'completedResponses as completed_responses_count',
            ])
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
