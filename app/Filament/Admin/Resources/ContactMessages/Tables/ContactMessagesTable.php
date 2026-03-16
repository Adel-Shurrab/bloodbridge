<?php

namespace App\Filament\Admin\Resources\ContactMessages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\ContactMessage;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->getStateUsing(fn($record, $livewire) => $record->getTranslation('name', $livewire?->activeLocale ?? app()->getLocale(), false) ?: $record->getTranslation('name', 'ar', false))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable(),
                TextColumn::make('subject')
                    ->label(__('Subject'))
                    ->getStateUsing(fn($record, $livewire) => $record->getTranslation('subject', $livewire?->activeLocale ?? app()->getLocale(), false) ?: $record->getTranslation('subject', 'ar', false))
                    ->searchable(),
                TextColumn::make('read_at')
                    ->label(__('Read At'))
                    ->dateTime()
                    ->sortable()
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'warning')
                    ->placeholder(__('Unread')),
                TextColumn::make('created_at')
                    ->label(__('Sent At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->label(__('View')),
                Action::make('mark_as_read')
                    ->label(__('Mark as Read'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn(ContactMessage $record) => $record->update(['read_at' => now()]))
                    ->hidden(fn(ContactMessage $record) => $record->read_at !== null),
                DeleteAction::make()->label(__('Delete')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label(__('Delete Selected')),
                ]),
            ]);
    }
}
