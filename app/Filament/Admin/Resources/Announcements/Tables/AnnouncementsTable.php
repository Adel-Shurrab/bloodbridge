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
                    ->label('عنوان الإعلان')
                    ->searchable(),

                TextColumn::make('target_type')
                    ->label('الجمهور المستهدف')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'all' => 'الجميع',
                        'role' => 'دور محدد',
                        'specific_users' => 'مستخدمين محددين',
                        default => $state
                    }),

                IconColumn::make('send_via_email')
                    ->label('البريد')
                    ->boolean(),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn($state) => $state === 1 ? 'success' : 'warning')
                    ->formatStateUsing(fn($state) => $state === 1 ? 'منشور' : 'مسودة'),

                TextColumn::make('published_at')
                    ->label('تاريخ النشر')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->label('عرض'),
                Action::make('publish')
                    ->label('إرسال')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('تأكيد الإرسال')
                    ->modalDescription('هل أنت متأكد من رغبتك في إرسال هذا الإعلان؟ سيتم إرساله فوراً لجميع المستخدمين المستهدفين ولن تتمكن من تعديله بعد ذلك.')
                    ->modalSubmitActionLabel('نعم، أرسل الآن')
                    ->action(fn(Announcement $record) => $record->update(['status' => 1]))
                    ->visible(fn(Announcement $record) => $record->status === 0),
                EditAction::make()
                    ->label('تعديل')
                    ->hidden(fn(Announcement $record) => $record->status === 1),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
