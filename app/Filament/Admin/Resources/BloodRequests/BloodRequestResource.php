<?php

namespace App\Filament\Admin\Resources\BloodRequests;

use App\Filament\Admin\Resources\BloodRequests\Pages\EditBloodRequest;
use App\Filament\Admin\Resources\BloodRequests\Pages\ListBloodRequests;
use App\Filament\Admin\Resources\BloodRequests\Pages\ViewBloodRequest;
use App\Filament\Admin\Resources\BloodRequests\Schemas\BloodRequestForm;
use App\Filament\Admin\Resources\BloodRequests\Schemas\BloodRequestInfolist;
use App\Filament\Admin\Resources\BloodRequests\Tables\BloodRequestsTable;
use App\Models\BloodRequest;
use Filament\Resources\Resource;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;
use App\Filament\Admin\Resources\BloodRequests\RelationManagers;

class BloodRequestResource extends Resource
{
    use Translatable;

    protected static ?string $model = BloodRequest::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.blood_requests.title');
    }

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

    protected static ?string $recordTitleAttribute = 'additional_notes';

    public static function getTranslatableLocales(): array
    {
        return ['ar', 'en'];
    }

    public static function form(Schema $schema): Schema
    {
        return BloodRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BloodRequestsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BloodRequestInfolist::configure($schema);
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
            'edit' => EditBloodRequest::route('/{record}/edit'),
            'view' => ViewBloodRequest::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->withCount([
                'responses',
                'acceptedResponses as accepted_responses_count',
                'completedResponses as completed_responses_count',
            ]);
    }
}
