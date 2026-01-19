<?php

namespace App\Filament\Resources\BloodRequests\RelationManagers;

use App\Models\RequestResponse;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ResponsesRelationManager extends RelationManager
{
    protected static string $relationship = 'responses';

    protected static ?string $title = 'استجابات المتبرعين';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('status')
                    ->label('الحالة')
                    ->options(RequestResponse::getStatusOptions())
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('donor.user.name')
                    ->label('المتبرع')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn($state) => RequestResponse::getStatusOptions()[$state] ?? $state)
                    ->color(fn(string $state): string => match ((int) $state) {
                        RequestResponse::STATUS_COMPLETED => 'success',
                        RequestResponse::STATUS_ACCEPTED => 'info',
                        RequestResponse::STATUS_DECLINED, RequestResponse::STATUS_NO_SHOW, RequestResponse::STATUS_IGNORED => 'danger',
                        default => 'warning',
                    })
                    ->description(fn(RequestResponse $record) => $record->status == RequestResponse::STATUS_DECLINED ? $record->decline_reason : null),
                TextColumn::make('verified_at')
                    ->label('تم التحقق')
                    ->dateTime('Y/m/d h:i A')
                    ->placeholder('لم يتم التحقق')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('تاريخ الاستجابة')
                    ->dateTime('Y/m/d h:i A')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(RequestResponse::getStatusOptions()),
                TernaryFilter::make('verified_at')
                    ->label('حالة التحقق')
                    ->placeholder('الكل')
                    ->trueLabel('تم التحقق')
                    ->falseLabel('لم يتم التحقق')
                    ->queries(
                        true: fn($query) => $query->whereNotNull('verified_at'),
                        false: fn($query) => $query->whereNull('verified_at'),
                    ),
            ])
            ->headerActions([
                //
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }
}
