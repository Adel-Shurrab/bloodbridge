<?php

namespace App\Filament\Resources\Organizations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\ActionGroup;
use Filament\Tables\Table;
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
                    ->color(fn(int $state): string => match ($state) {
                        Organization::STATUS_APPROVED => 'success',
                        Organization::STATUS_REJECTED => 'danger',
                        Organization::STATUS_PENDING => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(int $state): string => Organization::getStatusOptions()[$state] ?? '-')
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
                    ->options(Organization::getStatusOptions()),
                \Filament\Tables\Filters\SelectFilter::make('governorate_id')
                    ->label('المحافظة')
                    ->relationship('governorate', 'name'),
                \Filament\Tables\Filters\TrashedFilter::make()->label('المحذوفات'),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()->label('عرض')->color('gray'),
                    EditAction::make()->label('تعديل'),
                    \Filament\Actions\DeleteAction::make()->label('حذف'),
                    \Filament\Actions\RestoreAction::make()->label('استعادة'),
                    \Filament\Actions\ForceDeleteAction::make()->label('حذف نهائي'),

                    // زر الموافقة
                    Action::make('approve')
                        ->label('موافقة')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn(Organization $record) => $record->approval_status === Organization::STATUS_PENDING)
                        ->action(function (Organization $record) {
                            $record->update(['approval_status' => Organization::STATUS_APPROVED]);
                            Notification::make()->title('تمت الموافقة')->success()->send();
                        }),

                    // زر الرفض
                    Action::make('reject')
                        ->label('رفض')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn(Organization $record) => $record->approval_status === Organization::STATUS_PENDING)
                        ->action(function (Organization $record) {
                            $record->update(['approval_status' => Organization::STATUS_REJECTED]);
                            Notification::make()->title('تم الرفض')->danger()->send();
                        }),
                ])->label('خيارات')->icon('heroicon-m-ellipsis-vertical')->color('gray')->button(),
            ]);
    }
}
