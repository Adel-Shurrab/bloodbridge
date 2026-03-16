<?php

namespace App\Filament\Admin\Resources\Donors\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ResponsesRelationManager extends RelationManager
{
    protected static string $relationship = 'responses';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('Response History');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('status')
                    ->label(__('Status'))
                    ->options(\App\Enums\RequestResponseStatus::class)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('bloodRequest.organization.org_name')
                    ->label(__('Organization'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('bloodRequest.urgency_level')
                    ->label(__('Urgency Level'))
                    ->badge(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge(),
                TextColumn::make('created_at')
                    ->label(__('Response Date'))
                    ->dateTime('Y/m/d h:i A')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(\App\Enums\RequestResponseStatus::class),
            ])
            ->headerActions([])
            ->recordActions([
                ViewAction::make(),
            ])
            ->bulkActions([]);
    }
}
