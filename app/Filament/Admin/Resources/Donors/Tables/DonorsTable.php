<?php

namespace App\Filament\Admin\Resources\Donors\Tables;

use App\Models\Donor;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                    ->badge(),
                TextColumn::make('healthProfile.blood_type')
                    ->label('فصيلة الدم')
                    ->badge()
                    ->sortable(),
                TextColumn::make('healthProfile.total_donations')
                    ->label('إجمالي التبرعات')
                    ->formatStateUsing(fn($state) => $state ?? 0)
                    ->badge()
                    ->color(fn($state) => match (true) {
                        $state >= 10 => 'success',
                        $state >= 5 => 'info',
                        $state >= 1 => 'warning',
                        default => 'gray',
                    })
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
                SelectFilter::make('gender')
                    ->label('الجنس')
                    ->options(\App\Enums\Gender::class),
                SelectFilter::make('governorate_id')
                    ->label('المحافظة')
                    ->relationship('governorate', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('blood_type')
                    ->label('فصيلة الدم')
                    ->options(\App\Enums\BloodType::class)
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value'])) {
                            return $query;
                        }
                        return $query->whereHas('healthProfile', function ($query) use ($data) {
                            $query->where('blood_type', '=', $data['value'], 'and');
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
                ]),
            ]);
    }
}
