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
                    ->label(__('admin.announcement_title'))
                    ->getStateUsing(fn($record, $livewire) => $record->getTranslation('title', $livewire?->activeLocale ?? app()->getLocale(), false) ?: $record->getTranslation('title', 'ar', false))
                    ->searchable(),

                TextColumn::make('target_type')
                    ->label(__('admin.target_audience'))
                    ->formatStateUsing(fn($state) => match ($state) {
                        'all' => __('admin.all'),
                        'role' => __('admin.specific_role'),
                        'specific_users' => __('admin.specific_users'),
                        default => $state
                    }),

                IconColumn::make('send_via_email')
                    ->label(__('admin.email'))
                    ->boolean(),

                TextColumn::make('status')
                    ->label(__('organization.status'))
                    ->badge()
                    ->color(fn($state) => $state === 1 ? 'success' : 'warning')
                    ->formatStateUsing(fn($state) => $state === 1 ? __('admin.published') : __('admin.draft')),

                TextColumn::make('published_at')
                    ->label(__('admin.published_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->label(__('admin.view')),
                Action::make('publish')
                    ->label(__('admin.send'))
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(__('admin.confirm_send'))
                    ->modalDescription(__('admin.send_announcement_confirmation_long'))
                    ->modalSubmitActionLabel(__('admin.yes_send_now'))
                    ->action(fn(Announcement $record) => $record->update(['status' => 1]))
                    ->visible(fn(Announcement $record) => $record->status === 0),
                EditAction::make()
                    ->label(__('admin.edit'))
                    ->hidden(fn(Announcement $record) => $record->status === 1),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
