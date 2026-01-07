<?php

namespace App\Filament\Resources\Donors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction; // Using generic DeleteAction which handles soft deletes
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;
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
                    ->date('Y/m/d')
                    ->sortable(),
                TextColumn::make('points')
                    ->label('النقاط')
                    ->formatStateUsing(fn($state) => number_format($state))
                    ->sortable(),
                TextColumn::make('level')
                    ->label('المستوى')
                    ->formatStateUsing(fn($state) => number_format($state))
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
                \Filament\Tables\Filters\SelectFilter::make('gender')
                    ->label('الجنس')
                    ->options(\App\Models\Donor::getGenderOptions()),
                \Filament\Tables\Filters\SelectFilter::make('governorate_id')
                    ->label('المحافظة')
                    ->relationship('governorate', 'name')
                    ->searchable()
                    ->preload(),
                \Filament\Tables\Filters\SelectFilter::make('blood_type')
                    ->label('فصيلة الدم')
                    ->options(\App\Models\Donor::getBloodTypeOptions())
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data) {
                        if (empty($data['value'])) {
                            return $query;
                        }
                        return $query->whereHas('healthProfile', function ($query) use ($data) {
                            $query->where('blood_type', $data['value']);
                        });
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()->label('عرض')->color('gray'),
                    EditAction::make()->label('تعديل'),
                    DeleteAction::make()->label('حذف'),
                    RestoreAction::make()->label('استعادة'),
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
