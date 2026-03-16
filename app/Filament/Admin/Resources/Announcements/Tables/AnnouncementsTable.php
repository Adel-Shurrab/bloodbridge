<?php

namespace App\Filament\Admin\Resources\Announcements\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\Action;
use App\Models\Announcement;

class AnnouncementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('Announcement Title'))
                    ->getStateUsing(fn($record, $livewire) => $record->getTranslation('title', $livewire?->activeLocale ?? app()->getLocale(), false) ?: $record->getTranslation('title', 'ar', false))
                    ->searchable(),

                TextColumn::make('target_type')
                    ->label(__('Target Audience'))
                    ->formatStateUsing(fn($state) => match ($state) {
                        'all' => __('All'),
                        'role' => __('Specific Role'),
                        'specific_users' => __('Specific Users'),
                        default => $state
                    }),

                IconColumn::make('send_via_email')
                    ->label(__('Email'))
                    ->boolean(),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn($state) => $state === 1 ? 'success' : 'warning')
                    ->formatStateUsing(fn($state) => $state === 1 ? __('Published') : __('Draft')),

                TextColumn::make('published_at')
                    ->label(__('Published At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->label(__('View')),
                Action::make('publish')
                    ->label(__('Send'))
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(__('Confirm Send'))
                    ->modalDescription(__('Are you sure you want to send this announcement? It will be sent immediately to all targeted users and you will not be able to edit it afterwards.'))
                    ->modalSubmitActionLabel(__('Yes, send now'))
                    ->action(fn(Announcement $record) => $record->update(['status' => 1]))
                    ->visible(fn(Announcement $record) => $record->status === 0),
                EditAction::make()
                    ->label(__('Edit'))
                    ->hidden(fn(Announcement $record) => $record->status === 1),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
