<?php

namespace App\Filament\Admin\Resources\Donors\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DonorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('Name'))
                    ->description(fn($record) => $record->user?->email)
                    ->getStateUsing(fn($record) => $record->user?->name)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('national_id')
                    ->label(__('National ID'))
                    ->searchable(),
                TextColumn::make('gender')
                    ->label(__('Gender'))
                    ->badge(),
                TextColumn::make('healthProfile.blood_type')
                    ->label(__('Blood Type'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('healthProfile.total_donations')
                    ->label(__('Total Donations'))
                    ->formatStateUsing(fn($state) => $state ?? 0)
                    ->badge()
                    ->color(fn($state) => match (true) {
                        $state >= 10 => 'success',
                        $state >= 5 => 'info',
                        $state >= 1 => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('governorate.name')
                    ->label(__('Governorate'))
                    ->getStateUsing(fn($record) => $record->governorate?->getTranslation('name', app()->getLocale(), false) ?: $record->governorate?->getTranslation('name', 'ar', false))
                    ->sortable(),
                TextColumn::make('birth_date')
                    ->label(__('Birth Date'))
                    ->date('Y/m/d')
                    ->sortable(),
                TextColumn::make('points')
                    ->label(__('Points'))
                    ->formatStateUsing(fn($state) => number_format($state))
                    ->sortable(),
                TextColumn::make('level')
                    ->label(__('Level'))
                    ->formatStateUsing(fn($state) => number_format($state))
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
            ->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('gender')
                    ->label(__('Gender'))
                    ->options(\App\Enums\Gender::class),
                SelectFilter::make('governorate_id')
                    ->label(__('Governorate'))
                    ->relationship('governorate', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('blood_type')
                    ->label(__('Blood Type'))
                    ->options(\App\Enums\BloodType::class)
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value'])) {
                            return $query;
                        }
                        return $query->whereHas('healthProfile', function ($query) use ($data) {
                            $query->where('blood_type', '=', $data['value'], 'and');
                        });
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()->label(__('View'))->color('gray'),
                    EditAction::make()->label(__('Edit')),
                    DeleteAction::make()->label(__('Delete')),
                    RestoreAction::make()->label(__('Restore')),
                ])->label(__('Actions'))->icon('heroicon-m-ellipsis-vertical')->color('gray')->button(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label(__('Delete')),
                    RestoreBulkAction::make()->label(__('Restore')),
                ]),
            ]);
    }
}
