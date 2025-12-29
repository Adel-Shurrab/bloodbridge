<?php

namespace App\Filament\Resources\Organizations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Resources\Resources;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\Organization;

class OrganizationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('org_name')
                    ->label('المنظمة')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('governorate.name')
                    ->label('المحافظة')
                    ->sortable(),

                TextColumn::make('contact_phone')
                    ->label('الهاتف')
                    ->searchable(),

                TextColumn::make('daily_capacity')
                    ->label('القدرة اليومية')
                    ->sortable(),

                TextColumn::make('approval_status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('approval_status')
                    ->label('حالة الموافقة')
                    ->options([
                        'pending' => 'قيد الانتظار',
                        'approved' => 'مقبول',
                        'rejected' => 'مرفوض',
                    ]),
                \Filament\Tables\Filters\SelectFilter::make('governorate_id')
                    ->label('المحافظة')
                    ->relationship('governorate', 'name'),
            ])
            ->actions([
                \Filament\Tables\Actions\ActionGroup::make([
                    \Filament\Tables\Actions\ViewAction::make()->label('عرض')->color('gray'),
                    \Filament\Tables\Actions\EditAction::make()->label('تعديل'),

                    // زر الموافقة
                    Action::make('approve')
                        ->label('موافقة')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn(Organization $record) => $record->approval_status === 'pending')
                        ->action(function (Organization $record) {
                            $record->update(['approval_status' => 'approved']);
                            Notification::make()->title('تمت الموافقة')->success()->send();
                        }),

                    // زر الرفض
                    Action::make('reject')
                        ->label('رفض')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn(Organization $record) => $record->approval_status === 'pending')
                        ->action(function (Organization $record) {
                            $record->update(['approval_status' => 'rejected']);
                            Notification::make()->title('تم الرفض')->danger()->send();
                        }),
                ])->label('خيارات')->icon('heroicon-m-ellipsis-vertical')->color('gray')->button(),
            ]);
    }
}
