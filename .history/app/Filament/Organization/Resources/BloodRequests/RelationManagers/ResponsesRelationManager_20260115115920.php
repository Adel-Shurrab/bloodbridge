<?php

namespace App\Filament\Organization\Resources\BloodRequests\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ResponsesRelationManager extends RelationManager
{
    protected static string $relationship = 'responses';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('donor_id')
                    ->required()
                    ->numeric(),
                TextInput::make('status')
                    ->required()
                    ->numeric()
                    ->default(0),
                DateTimePicker::make('responded_at'),
                Textarea::make('decline_reason')
                    ->default(null)
                    ->columnSpanFull(),
                Toggle::make('verification_qr_code')
                    ->required(),
                DateTimePicker::make('verified_at'),
                TextInput::make('appointment_id')
                    ->numeric()
                    ->default(null),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('donor_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('responded_at')
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('verification_qr_code')
                    ->boolean(),
                TextColumn::make('verified_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('appointment_id')
                    ->numeric()
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
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
