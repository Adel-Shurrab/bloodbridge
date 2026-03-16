<?php

namespace App\Filament\Admin\Resources\BloodRequests\Tables;

use App\Models\BloodRequest;
use App\Models\Donor;
use Filament\Tables;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BloodRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('additional_notes')
            ->columns([
                TextColumn::make('organization.org_name')
                    ->label(__('Organization Name'))
                    ->getStateUsing(fn($record, $livewire) => $record->organization ? ($record->organization->getTranslation('org_name', $livewire?->activeLocale ?? app()->getLocale(), false) ?: $record->organization->getTranslation('org_name', 'ar', false)) : null)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('blood_type')
                    ->label(__('Blood Type'))
                    ->badge(),
                TextColumn::make('units_needed')
                    ->label(__('Units'))
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
                TextColumn::make('created_at')
                    ->label(__('Request Date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TrashedFilter::make()->label(__('Trashed')),
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(\App\Enums\BloodRequestStatus::class),
                Tables\Filters\SelectFilter::make('urgency_level')
                    ->label(__('Urgency'))
                    ->options(\App\Enums\UrgencyLevel::class),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()->label(__('View'))->color('gray'),
                    EditAction::make()->label(__('Edit')),
                    DeleteAction::make()->label(__('Delete')),
                    RestoreAction::make()->label(__('Restore')),
                ])->label(__('Options'))->icon('heroicon-m-ellipsis-vertical')->color('gray')->button(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label(__('Delete')),
                    RestoreBulkAction::make()->label(__('Restore')),
                ]),
            ]);
    }
}
