<?php

namespace App\Filament\Organization\Resources\BloodRequests\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class BloodRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
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
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
