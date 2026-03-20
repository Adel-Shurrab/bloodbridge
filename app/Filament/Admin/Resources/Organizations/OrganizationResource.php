<?php

namespace App\Filament\Admin\Resources\Organizations;

use App\Filament\Admin\Resources\Organizations\Pages\ListOrganizations;
use App\Filament\Admin\Resources\Organizations\Schemas\OrganizationForm;
use App\Filament\Admin\Resources\Organizations\Schemas\OrganizationInfolist;
use App\Filament\Admin\Resources\Organizations\Tables\OrganizationsTable;
use App\Filament\Admin\Resources\Organizations\RelationManagers\BloodRequestsRelationManager;
use App\Models\Organization;
use BackedEnum;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OrganizationResource extends Resource
{
    use Translatable;

    protected static ?string $model = Organization::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingOffice;

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.organizations.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.operations');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.organizations.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.organizations.plural');
    }

    public static function getTranslatableLocales(): array
    {
        return ['ar', 'en'];
    }

    public static function form(Schema $schema): Schema
    {
        $formSchema = OrganizationForm::configure(Schema::make());
        $components = $formSchema->getComponents();

        return $schema
            ->components([
                Grid::make(['lg' => 3])
                    ->schema([
                        Group::make([
                            ($components[0] ?? null)?->visible(fn($operation) => $operation === 'create'),
                            $components[1] ?? null,
                            $components[2] ?? null,
                            $components[3] ?? null,
                        ])->columnSpan(['lg' => 2]),

                        Group::make([
                            Section::make(__('Status'))
                                ->schema([
                                    Select::make('approval_status')
                                        ->label(__('Approval Status'))
                                        ->options(\App\Enums\OrganizationStatus::class)
                                        ->default(\App\Enums\OrganizationStatus::PENDING)
                                        ->required()
                                        ->columnSpanFull(),
                                ]),

                            Section::make(__('System Data'))
                                ->schema([
                                    TextInput::make('created_at')
                                        ->label(__('Registration Date'))
                                        ->disabled()
                                        ->visible(fn($record) => $record !== null),

                                    TextInput::make('updated_at')
                                        ->label(__('Last Update'))
                                        ->disabled()
                                        ->visible(fn($record) => $record !== null),
                                ]),
                        ])->columnSpan(['lg' => 1]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return OrganizationsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OrganizationInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [
            BloodRequestsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrganizations::route('/'),
            'create' => \App\Filament\Admin\Resources\Organizations\Pages\CreateOrganization::route('/create'),
            'view' => \App\Filament\Admin\Resources\Organizations\Pages\ViewOrganization::route('/{record}'),
            'edit' => \App\Filament\Admin\Resources\Organizations\Pages\EditOrganization::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                \Illuminate\Database\Eloquent\SoftDeletingScope::class,
            ]);
    }
}
