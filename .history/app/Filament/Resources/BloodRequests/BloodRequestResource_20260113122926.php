<?php

namespace App\Filament\Resources\BloodRequests;

use App\Filament\Resources\BloodRequests\Pages\ManageBloodRequests;
use App\Models\BloodRequest;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BloodRequestResource extends Resource
{
    protected static ?string $model = BloodRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'additional_notes';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('organization_id')
                    ->required()
                    ->numeric(),
                TextInput::make('blood_type')
                    ->required()
                    ->numeric(),
                TextInput::make('units_needed')
                    ->required()
                    ->numeric(),
                TextInput::make('urgency_level')
                    ->required()
                    ->numeric()
                    ->default(1),
                Textarea::make('additional_notes')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('search_radius_km')
                    ->required()
                    ->numeric()
                    ->default(10),
                TextInput::make('status')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('donors_accepted')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('donors_completed')
                    ->required()
                    ->numeric()
                    ->default(0),
                DateTimePicker::make('broadcasted_at'),
                DateTimePicker::make('fulfilled_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('additional_notes')
            ->columns([
                TextColumn::make('organization_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('blood_type')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('units_needed')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('urgency_level')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('search_radius_km')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('donors_accepted')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('donors_completed')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('broadcasted_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('fulfilled_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageBloodRequests::route('/'),
        ];
    }
}
