<?php

namespace App\Filament\Resources\BloodRequests\Tables;

use App\Models\BloodRequest;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Filters\SelectFilter;

class BloodRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('additional_notes')
            ->columns([
                TextColumn::make('organization.org_name')
                    ->label('المنظمة')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('blood_type')
                    ->label('فصيلة الدم')
                    ->formatStateUsing(fn($state) => \App\Models\Donor::getBloodTypeOptions()[$state] ?? $state)
                    ->badge()
                    ->color('danger'),
                TextColumn::make('units_needed')
                    ->label('الوحدات')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('urgency_level')
                    ->label('الاستعجال')
                    ->formatStateUsing(fn($state) => BloodRequest::getUrgencyOptions()[$state] ?? $state)
                    ->badge()
                    ->color(fn(string $state): string => match ((int)$state) {
                        BloodRequest::URGENCY_CRITICAL => 'danger',
                        BloodRequest::URGENCY_HIGH => 'warning',
                        default => 'success',
                    })
                    ->sortable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn($state) => BloodRequest::getStatusOptions()[$state] ?? $state)
                    ->badge()
                    ->color(fn(string $state): string => match ((int)$state) {
                        BloodRequest::STATUS_FULFILLED => 'success',
                        BloodRequest::STATUS_CANCELLED, BloodRequest::STATUS_EXPIRED => 'gray',
                        default => 'warning',
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('تاريخ الطلب')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(BloodRequest::getStatusOptions()),
                Tables\Filters\SelectFilter::make('urgency_level')
                    ->label('الاستعجال')
                    ->options(BloodRequest::getUrgencyOptions()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
