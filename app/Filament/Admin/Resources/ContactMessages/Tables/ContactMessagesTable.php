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
                    ->label(__('admin.name'))
                    ->getStateUsing(fn($record, $livewire) => $record->getTranslation('name', $livewire?->activeLocale ?? app()->getLocale(), false) ?: $record->getTranslation('name', 'ar', false))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('admin.email'))
                    ->searchable(),
                TextColumn::make('subject')
                    ->label(__('admin.subject'))
                    ->getStateUsing(fn($record, $livewire) => $record->getTranslation('subject', $livewire?->activeLocale ?? app()->getLocale(), false) ?: $record->getTranslation('subject', 'ar', false))
                    ->searchable(),
                TextColumn::make('read_at')
                    ->label(__('admin.read_at'))
                    ->dateTime()
                    ->sortable()
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'warning')
                    ->placeholder(__('admin.unread')),
                TextColumn::make('created_at')
                    ->label(__('admin.sent_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->label(__('admin.view')),
                Action::make('mark_as_read')
                    ->label(__('admin.mark_as_read'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn(ContactMessage $record) => $record->update(['read_at' => now()]))
                    ->hidden(fn(ContactMessage $record) => $record->read_at !== null),
                DeleteAction::make()->label(__('admin.delete')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label(__('admin.delete_selected')),
                ]),
            ]);
    }
}
