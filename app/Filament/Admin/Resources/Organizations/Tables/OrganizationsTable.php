<?php

namespace App\Filament\Admin\Resources\Organizations\Tables;

use App\Models\Organization;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class OrganizationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('org_name')
                    ->label(__('Organization'))
                    ->weight('bold')
                    ->getStateUsing(fn($record) => $record->getTranslation('org_name', app()->getLocale(), false) ?: $record->getTranslation('org_name', 'ar', false))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('governorate.name')
                    ->label(__('Governorate'))
                    ->getStateUsing(fn($record) => $record->governorate?->getTranslation('name', app()->getLocale(), false) ?: $record->governorate?->getTranslation('name', 'ar', false))
                    ->sortable(),

                TextColumn::make('contact_phone')
                    ->label(__('Phone'))
                    ->searchable(),

                TextColumn::make('daily_capacity')
                    ->label(__('Daily Capacity'))
                    ->sortable(),

                TextColumn::make('approval_status')
                    ->label(__('Status'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('Registration Date'))
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('approval_status')
                    ->label(__('Approval Status'))
                    ->options(\App\Enums\OrganizationStatus::class),
                SelectFilter::make('governorate_id')
                    ->label(__('Governorate'))
                    ->relationship('governorate', 'name'),
                TrashedFilter::make()->label(__('Trash')),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()->label(__('View'))->color('gray'),
                    EditAction::make()->label(__('Edit')),
                    DeleteAction::make()->label(__('Delete')),
                    RestoreAction::make()->label(__('Restore')),

                    Action::make('approve')
                        ->label(__('Approve'))
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn(Organization $record) => $record->approval_status === \App\Enums\OrganizationStatus::PENDING)
                        ->action(function (Organization $record) {
                            $record->approval_status = \App\Enums\OrganizationStatus::APPROVED;
                            $record->save();
                            Notification::make()->title(__('Approved Successfully'))->success()->send();
                        }),

                    Action::make('reject')
                        ->label(__('Reject'))
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn(Organization $record) => $record->approval_status === \App\Enums\OrganizationStatus::PENDING)
                        ->action(function (Organization $record) {
                            $record->approval_status = \App\Enums\OrganizationStatus::REJECTED;
                            $record->save();
                            Notification::make()->title(__('Rejected Successfully'))->danger()->send();
                        }),
                ])->label(__('Actions'))->icon('heroicon-m-ellipsis-vertical')->color('gray')->button(),
            ]);
    }
}
