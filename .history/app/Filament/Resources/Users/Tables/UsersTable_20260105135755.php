<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable(),

                \Filament\Tables\Columns\TextColumn::make('role')
                    ->label('الصلاحية')
                    ->badge()
                    ->formatStateUsing(fn($state) => \App\Models\User::getRoleOptions()[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        \App\Models\User::ROLE_ADMIN => 'danger',
                        \App\Models\User::ROLE_ORGANIZATION => 'success',
                        \App\Models\User::ROLE_DONOR => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                \Filament\Tables\Columns\ToggleColumn::make('is_active')
                    ->label('نشط'),

                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('role')
                    ->label('الصلاحية')
                    ->options(\App\Models\User::getRoleOptions()),
                \Filament\Tables\Filters\TernaryFilter::make('is_active')
                    ->label('نشط'),
                \Filament\Tables\Filters\TrashedFilter::make()->label('المحذوفات'),
            ])
            ->actions([
                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\ViewAction::make()->label('عرض')->color('gray'),
                    \Filament\Actions\EditAction::make()->label('تعديل'),
                    \Filament\Actions\DeleteAction::make()->label('حذف'),
                    \Filament\Actions\RestoreAction::make()->label('استعادة'),
                    \Filament\Actions\ForceDeleteAction::make()->label('حذف نهائي'),
                ])->label('خيارات')->icon('heroicon-m-ellipsis-vertical')->color('gray')->button(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('حذف'),
                    \Filament\Tables\Actions\RestoreBulkAction::make()->label('استعادة'),
                    \Filament\Tables\Actions\ForceDeleteBulkAction::make()->label('حذف نهائي'),
                ]),
            ]);
    }
}
