<?php

namespace App\Filament\Admin\Resources\Organizations\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BloodRequestsRelationManager extends RelationManager
{
    protected static string $relationship = 'bloodRequests';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('Blood Requests (Relation)');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('blood_type')
                    ->label(__('Blood Type'))
                    ->options(\App\Enums\BloodType::class)
                    ->required(),
                TextInput::make('units_needed')
                    ->label(__('Units Needed'))
                    ->numeric()
                    ->required()
                    ->minValue(1),
                \Filament\Forms\Components\Select::make('urgency_level')
                    ->label(__('Urgency Level'))
                    ->options(\App\Enums\UrgencyLevel::class)
                    ->required(),
                \Filament\Forms\Components\Textarea::make('additional_notes')
                    ->label(__('Additional Notes'))
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('blood_type')
                    ->label(__('Blood Type'))
                    ->badge(),
                TextColumn::make('units_needed')
                    ->label(__('Units'))
                    ->sortable(),
                TextColumn::make('urgency_level')
                    ->label(__('Urgency'))
                    ->badge(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge(),
                TextColumn::make('created_at')
                    ->label(__('Order Date'))
                    ->dateTime('Y/m/d h:i A')
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(\App\Enums\BloodRequestStatus::class),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('Create New Request')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([]);
    }
}
