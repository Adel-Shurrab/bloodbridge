<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable(),

                TextColumn::make('role')
                    ->label('نوع الحساب')
                    ->badge()
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label('نشط'),

                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('نوع الحساب')
                    ->options(\App\Enums\UserRole::class),
                TernaryFilter::make('is_active')
                    ->label('نشط'),
                TrashedFilter::make()->label('المحذوفات'),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()->label('عرض')->color('gray'),
                    EditAction::make()->label('تعديل'),
                    DeleteAction::make()->label('حذف'),
                    RestoreAction::make()->label('استعادة'),
                    ForceDeleteAction::make()->label('حذف نهائي'),
                ])->label('خيارات')->icon('heroicon-m-ellipsis-vertical')->color('gray')->button(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('حذف'),
                    RestoreBulkAction::make()->label('استعادة'),
                    ForceDeleteBulkAction::make()->label('حذف نهائي'),
                ]),
            ]);
    }
}
