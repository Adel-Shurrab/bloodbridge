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
                    ->label(__('admin.name'))
                    ->description(fn($record) => $record->user?->email)
                    ->getStateUsing(fn($record) => $record->user?->name)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('national_id')
                    ->label(__('admin.national_id'))
                    ->searchable(),
                TextColumn::make('gender')
                    ->label(__('admin.gender'))
                    ->badge(),
                TextColumn::make('healthProfile.blood_type')
                    ->label(__('organization.blood_type'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('healthProfile.total_donations')
                    ->label(__('admin.total_donations'))
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
                    ->label(__('admin.governorate'))
                    ->getStateUsing(fn($record) => $record->governorate?->getTranslation('name', app()->getLocale(), false) ?: $record->governorate?->getTranslation('name', 'ar', false))
                    ->sortable(),
                TextColumn::make('birth_date')
                    ->label(__('admin.birth_date'))
                    ->date('Y/m/d')
                    ->sortable(),
                TextColumn::make('points')
                    ->label(__('admin.points'))
                    ->formatStateUsing(fn($state) => number_format($state))
                    ->sortable(),
                TextColumn::make('level')
                    ->label(__('admin.level'))
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
                    ->label(__('admin.gender'))
                    ->options(\App\Enums\Gender::class),
                SelectFilter::make('governorate_id')
                    ->label(__('admin.governorate'))
                    ->relationship('governorate', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('blood_type')
                    ->label(__('organization.blood_type'))
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
                    ViewAction::make()->label(__('admin.view'))->color('gray'),
                    EditAction::make()->label(__('admin.edit')),
                    DeleteAction::make()->label(__('admin.delete')),
                    RestoreAction::make()->label(__('admin.restore')),
                ])->label(__('admin.actions'))->icon('heroicon-m-ellipsis-vertical')->color('gray')->button(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label(__('admin.delete')),
                    RestoreBulkAction::make()->label(__('admin.restore')),
                ]),
            ]);
    }
}
