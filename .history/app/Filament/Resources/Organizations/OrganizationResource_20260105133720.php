<?php

namespace App\Filament\Resources\Organizations;

use App\Filament\Resources\Organizations\Pages\ListOrganizations;
use App\Filament\Resources\Organizations\Schemas\OrganizationForm;
use App\Filament\Resources\Organizations\Tables\OrganizationsTable;
use App\Models\Organization;
use BackedEnum;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingOffice;
    protected static ?string $navigationLabel = 'المنظمات';
    protected static ?string $modelLabel = 'منظمة';
    protected static ?string $pluralModelLabel = 'المنظمات';

    public static function form(Schema $schema): Schema
    {
        $formSchema = OrganizationForm::configure(Schema::make());
        $components = $formSchema->getComponents();

        return $schema
            ->components([
                Grid::make(['lg' => 3])
                    ->schema([
                        Group::make([
                            ($components[0] ?? null)?->visible(fn($operation) => $operation === 'create'), // حساب المستخدم
                            $components[1] ?? null, // معلومات المنظمة (Includes Responsible Person)
                            $components[2] ?? null, // معلومات التواصل العام
                            $components[3] ?? null, // الموقع وساعات العمل
                        ])->columnSpan(['lg' => 2]),

                        Group::make([
                            Section::make('الحالة')
                                ->schema([
                                    Select::make('approval_status')
                                        ->label('حالة الموافقة')
                                        ->options(Organization::getStatusOptions())
                                        ->default(Organization::STATUS_PENDING)
                                        ->required()
                                        ->columnSpanFull(),
                                ]),

                            Section::make('بيانات النظام')
                                ->schema([
                                    TextInput::make('created_at')
                                        ->label('تاريخ التسجيل')
                                        ->disabled()
                                        ->visible(fn($record) => $record !== null),

                                    TextInput::make('updated_at')
                                        ->label('آخر تحديث')
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrganizations::route('/'),
            'create' => \App\Filament\Resources\Organizations\Pages\CreateOrganization::route('/create'),
            'view' => \App\Filament\Resources\Organizations\Pages\ViewOrganization::route('/{record}'),
            'edit' => \App\Filament\Resources\Organizations\Pages\EditOrganization::route('/{record}/edit'),
        ];
    }
}
