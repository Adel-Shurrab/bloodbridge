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
                    ->label('الاسم')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable(),
                TextColumn::make('subject')
                    ->label('الموضوع')
                    ->searchable(),
                TextColumn::make('read_at')
                    ->label('تاريخ القراءة')
                    ->dateTime()
                    ->sortable()
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'warning')
                    ->placeholder('غير مقروءة'),
                TextColumn::make('created_at')
                    ->label('تاريخ الإرسال')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->label('عرض'),
                Action::make('mark_as_read')
                    ->label('تحديد كمقروء')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn(ContactMessage $record) => $record->update(['read_at' => now()]))
                    ->hidden(fn(ContactMessage $record) => $record->read_at !== null),
                DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('حذف المحدد'),
                ]),
            ]);
    }
}
