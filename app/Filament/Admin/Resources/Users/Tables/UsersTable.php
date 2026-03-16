<?php

namespace App\Filament\Admin\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->getStateUsing(fn($record) => $record->name)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label(__('Phone Number'))
                    ->searchable(),

                TextColumn::make('role')
                    ->label(__('Account Type'))
                    ->badge()
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label(__('Active')),

                TextColumn::make('created_at')
                    ->label(__('Creation Date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('role')
                    ->label(__('Account Type'))
                    ->options(\App\Enums\UserRole::class),
                TernaryFilter::make('is_active')
                    ->label(__('Active')),
                TrashedFilter::make()->label(__('Trashed')),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()->label(__('View'))->color('gray'),
                    EditAction::make()->label(__('Edit')),
                    DeleteAction::make()->label(__('Delete')),
                    RestoreAction::make()->label(__('Restore')),
                    ForceDeleteAction::make()->label(__('Force Delete')),
                ])->label(__('Options'))->icon('heroicon-m-ellipsis-vertical')->color('gray')->button(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label(__('Delete')),
                    RestoreBulkAction::make()->label(__('Restore')),
                    ForceDeleteBulkAction::make()->label(__('Force Delete')),
                ]),
            ]);
    }
}
