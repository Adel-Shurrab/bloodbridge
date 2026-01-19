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
                    ->formatStateUsing(fn($state) => $state?->getLabel() ?? '-')
                    ->badge()
                    ->sortable(),

                TextColumn::make('units_needed')
                    ->label('الوحدات المطلوبة')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('urgency_level')
                    ->label('الاستعجال')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => BloodRequest::getUrgencyOptions()[$state] ?? $state)
                    ->color(fn(string $state): string => match ((int)$state) {
                        BloodRequest::URGENCY_CRITICAL => 'danger',
                        BloodRequest::URGENCY_HIGH => 'warning',
                        default => 'success',
                    })
                    ->sortable(),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => BloodRequest::getStatusOptions()[$state] ?? $state)
                    ->color(fn(string $state): string => match ((int)$state) {
                        BloodRequest::STATUS_FULFILLED => 'success',
                        BloodRequest::STATUS_CANCELLED, BloodRequest::STATUS_EXPIRED => 'danger',
                        BloodRequest::STATUS_BROADCASTED, BloodRequest::STATUS_MATCHED => 'warning',
                        default => 'gray',
                    })
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
                    ->options(BloodRequest::getStatusOptions()),
                SelectFilter::make('urgency_level')
                    ->label('تصفية حسب الاستعجال')
                    ->options(BloodRequest::getUrgencyOptions()),
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
