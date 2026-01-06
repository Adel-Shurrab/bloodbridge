<?php

namespace App\Filament\Resources\Donors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class DonorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('national_id')
                    ->label('رقم الهوية')
                    ->searchable(),
                TextColumn::make('gender')
                    ->label('الجنس')
                    ->formatStateUsing(fn($state) => \App\Models\Donor::getGenderOptions()[$state] ?? $state),
                TextColumn::make('healthProfile.blood_type')
                    ->label('فصيلة الدم')
                    ->formatStateUsing(fn($state) => \App\Models\Donor::getBloodTypeOptions()[$state] ?? $state)
                    ->sortable(),
                TextColumn::make('governorate.name')
                    ->label('المحافظة')
                    ->sortable(),
                TextColumn::make('birth_date')
                    ->label('تاريخ الميلاد')
                    ->date()
                    ->sortable(),
                TextColumn::make('points')
                    ->label('النقاط')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('level')
                    ->label('المستوى')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
