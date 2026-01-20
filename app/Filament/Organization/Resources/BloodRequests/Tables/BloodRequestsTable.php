<?php

namespace App\Filament\Organization\Resources\BloodRequests\Tables;

use App\Models\BloodRequest;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\BloodType;

class BloodRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('blood_type')
                    ->label('فصيلة الدم')
                    ->badge()
                    ->sortable(),

                TextColumn::make('units_needed')
                    ->label('الوحدات المطلوبة')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('urgency_level')
                    ->label('الاستعجال')
                    ->badge()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->sortable(),

                TextColumn::make('donors_completed')
                    ->label('المتبرعين')
                    ->state(fn(BloodRequest $record) => "{$record->donors_completed} / {$record->units_needed}")
                    ->badge()
                    ->color(fn(BloodRequest $record) => $record->donors_completed >= $record->units_needed ? 'success' : 'info'),

                TextColumn::make('created_at')
                    ->label('تاريخ الطلب')
                    ->dateTime('d/m/Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('تصفية حسب الحالة')
                    ->options(\App\Enums\BloodRequestStatus::class),
                SelectFilter::make('urgency_level')
                    ->label('تصفية حسب الاستعجال')
                    ->options(\App\Enums\UrgencyLevel::class),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->toolbarActions([]);
    }
}
