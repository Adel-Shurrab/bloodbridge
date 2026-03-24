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
        return __('admin.blood_requests_relation');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('blood_type')
                    ->label(__('organization.blood_type'))
                    ->options(\App\Enums\BloodType::class)
                    ->required(),
                TextInput::make('units_needed')
                    ->label(__('donor.units_needed'))
                    ->numeric()
                    ->required()
                    ->minValue(1),
                \Filament\Forms\Components\Select::make('urgency_level')
                    ->label(__('admin.urgency_level'))
                    ->options(\App\Enums\UrgencyLevel::class)
                    ->required(),
                \Filament\Forms\Components\Textarea::make('additional_notes')
                    ->label(__('admin.additional_notes'))
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('blood_type')
                    ->label(__('organization.blood_type'))
                    ->badge(),
                TextColumn::make('units_needed')
                    ->label(__('admin.units'))
                    ->sortable(),
                TextColumn::make('urgency_level')
                    ->label(__('admin.urgency'))
                    ->badge(),
                TextColumn::make('status')
                    ->label(__('organization.status'))
                    ->badge(),
                TextColumn::make('created_at')
                    ->label(__('admin.request_date'))
                    ->dateTime('Y/m/d h:i A')
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->label(__('organization.status'))
                    ->options(\App\Enums\BloodRequestStatus::class),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('admin.create_new_request')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([]);
    }
}
