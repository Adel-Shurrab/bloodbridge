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
                    ->label(__('admin.name'))
                    ->getStateUsing(fn($record) => $record->name)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label(__('admin.email'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label(__('admin.phone_number'))
                    ->searchable(),

                TextColumn::make('role')
                    ->label(__('admin.account_type'))
                    ->badge()
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label(__('admin.active')),

                TextColumn::make('created_at')
                    ->label(__('admin.creation_date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('role')
                    ->label(__('admin.account_type'))
                    ->options(\App\Enums\UserRole::class),
                TernaryFilter::make('is_active')
                    ->label(__('admin.active')),
                TrashedFilter::make()->label(__('admin.trashed')),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()->label(__('admin.view'))->color('gray'),
                    EditAction::make()->label(__('admin.edit')),
                    DeleteAction::make()->label(__('admin.delete')),
                    RestoreAction::make()->label(__('admin.restore')),
                    ForceDeleteAction::make()->label(__('admin.force_delete')),
                ])->label(__('admin.options'))->icon('heroicon-m-ellipsis-vertical')->color('gray')->button(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label(__('admin.delete')),
                    RestoreBulkAction::make()->label(__('admin.restore')),
                    ForceDeleteBulkAction::make()->label(__('admin.force_delete')),
                ]),
            ]);
    }
}
