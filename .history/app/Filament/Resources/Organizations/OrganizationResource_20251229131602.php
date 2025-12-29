<?php

namespace App\Filament\Resources\Organizations;

use App\Filament\Resources\Organizations\Pages\ViewOrganization;
use App\Filament\Resources\Organizations\Schemas\OrganizationForm;
use App\Filament\Resources\Organizations\Tables\OrganizationsTable;
use App\Models\Organization;
use BackedEnum;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
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
        return $schema
            ->components([
                Split::make([
                    Group::make([
                        OrganizationForm::configure(Schema::make())->getComponents()[0], // معلومات المنظمة
                        OrganizationForm::configure(Schema::make())->getComponents()[1], // الشخص المسؤول
                        OrganizationForm::configure(Schema::make())->getComponents()[2], // معلومات التواصل العام
                        OrganizationForm::configure(Schema::make())->getComponents()[3], // الموقع وساعات العمل
                    ])->columnSpan(['lg' => 2]),

                    Group::make([
                        Section::make('الحالة')
                            ->schema([
                                TextInput::make('approval_status')
                                    ->label('حالة الموافقة')
                                    ->disabled()
                                    ->dehydrated(false),
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
