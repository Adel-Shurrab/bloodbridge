<?php

namespace App\Filament\Organization\Resources\BloodRequests\Tables;

use App\Models\BloodRequest;
use App\Models\Donor;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class BloodRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('blood_type')
                    ->label('فصيلة الدم')
                    ->formatStateUsing(fn($state) => Donor::getBloodTypeOptions()[$state] ?? $state)
                    ->sortable()
                    ->badge()
                    ->color('danger'),

                TextColumn::make('units_needed')
                    ->label('الاحتياج')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('urgency_level')
                    ->label('الاستعجال')
                    ->formatStateUsing(fn($state) => BloodRequest::getUrgencyOptions()[$state] ?? $state)
                    ->sortable()
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        BloodRequest::URGENCY_LOW => 'gray',
                        BloodRequest::URGENCY_MEDIUM => 'info',
                        BloodRequest::URGENCY_HIGH => 'warning',
                        BloodRequest::URGENCY_CRITICAL => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn($state) => BloodRequest::getStatusOptions()[$state] ?? $state)
                    ->sortable()
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        BloodRequest::STATUS_PENDING => 'gray',
                        BloodRequest::STATUS_BROADCASTED => 'info',
                        BloodRequest::STATUS_MATCHED => 'warning',
                        BloodRequest::STATUS_FULFILLED => 'success',
                        BloodRequest::STATUS_CANCELLED => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('donors_accepted')
                    ->label('المقبولين')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('donors_completed')
                    ->label('تبرعوا')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('تاريخ الطلب')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
