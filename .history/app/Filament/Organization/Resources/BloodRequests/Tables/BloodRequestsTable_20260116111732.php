<?php

namespace App\Filament\Organization\Resources\BloodRequests\Tables;

use App\Models\BloodRequest;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BloodRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->where('organization_id', auth()->user()->organization_id))
            ->columns([
                TextColumn::make('blood_type')
                    ->label('فصيلة الدم')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => BloodRequest::getBloodTypeOptions()[$state] ?? $state)
                    ->color(fn(string $state): string => match ((int)$state) {
                        Donor::BLOOD_TYPE_O_POSITIVE, Donor::BLOOD_TYPE_O_NEGATIVE => 'success',
                        default => 'primary',
                    })
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
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => BloodRequest::getStatusOptions()[$state] ?? $state)
                    ->color(fn(string $state): string => match ((int)$state) {
                        BloodRequest::STATUS_FULFILLED => 'success',
                        BloodRequest::STATUS_CANCELLED => 'danger',
                        BloodRequest::STATUS_BROADCASTED => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('donors_completed')
                    ->label('المتبرعين') // Completed / Accepted if needed, or just completed
                    ->state(fn(BloodRequest $record) => "{$record->donors_completed} / {$record->units_needed}")
                    ->badge()
                    ->color(fn(BloodRequest $record) => $record->donors_completed >= $record->units_needed ? 'success' : 'gray'),

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
            ->toolbarActions([
                // ExportAction will be added here later
            ]);
    }
}
