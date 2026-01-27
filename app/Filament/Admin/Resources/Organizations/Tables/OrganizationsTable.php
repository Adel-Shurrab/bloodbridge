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
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('approval_status')
                    ->label('حالة الموافقة')
                    ->options(\App\Enums\OrganizationStatus::class),
                SelectFilter::make('governorate_id')
                    ->label('المحافظة')
                    ->relationship('governorate', 'name'),
                TrashedFilter::make()->label('المحذوفات'),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()->label('عرض')->color('gray'),
                    EditAction::make()->label('تعديل'),
                    DeleteAction::make()->label('حذف'),
                    RestoreAction::make()->label('استعادة'),

                    // زر الموافقة
                    Action::make('approve')
                        ->label('موافقة')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn(Organization $record) => $record->approval_status === \App\Enums\OrganizationStatus::PENDING)
                        ->action(function (Organization $record) {
                            $record->approval_status = \App\Enums\OrganizationStatus::APPROVED;
                            $record->save();
                            Notification::make()->title('تمت الموافقة')->success()->send();
                        }),

                    // زر الرفض
                    Action::make('reject')
                        ->label('رفض')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn(Organization $record) => $record->approval_status === \App\Enums\OrganizationStatus::PENDING)
                        ->action(function (Organization $record) {
                            $record->approval_status = \App\Enums\OrganizationStatus::REJECTED;
                            $record->save();
                            Notification::make()->title('تم الرفض')->danger()->send();
                        }),
                ])->label('خيارات')->icon('heroicon-m-ellipsis-vertical')->color('gray')->button(),
            ]);
    }
}
