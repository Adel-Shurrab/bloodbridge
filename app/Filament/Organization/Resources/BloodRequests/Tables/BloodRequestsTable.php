<?php

namespace App\Filament\Organization\Resources\BloodRequests\Tables;

use App\Models\BloodRequest;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class BloodRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('blood_type')
                    ->label(__('Blood Type'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('units_needed')
                    ->label(__('Units Needed'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make('urgency_level')
                    ->label(__('Urgency'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('responses_count')
                    ->counts('responses')
                    ->label(__('Responses'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('donors_completed')
                    ->label(__('Donors'))
                    ->state(function (BloodRequest $record) {
                        $completed = $record->donors_completed;

                        return "{$completed} / {$record->units_needed}";
                    })
                    ->badge()
                    ->color(function (BloodRequest $record) {
                        $completed = $record->donors_completed;

                        return $completed >= $record->units_needed ? 'success' : 'info';
                    }),

                TextColumn::make('created_at')
                    ->label(__('Request Date'))
                    ->dateTime('d/m/Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Filter by Status'))
                    ->options(\App\Enums\BloodRequestStatus::class),
                SelectFilter::make('urgency_level')
                    ->label(__('Filter by Urgency'))
                    ->options(\App\Enums\UrgencyLevel::class),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->toolbarActions([]);
    }
}

