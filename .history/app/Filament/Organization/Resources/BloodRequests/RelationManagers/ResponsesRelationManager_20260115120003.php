<?php

namespace App\Filament\Organization\Resources\BloodRequests\RelationManagers;

use App\Models\Donor;
use App\Models\RequestResponse;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ResponsesRelationManager extends RelationManager
{
    protected static string $relationship = 'responses';

    protected static ?string $title = 'الاستجابات (المتبرعين)';

    protected static ?string $modelLabel = 'استجابة';

    protected static ?string $pluralModelLabel = 'الاستجابات';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Usually read-only or minimal editing here
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('donor.user.name')
                    ->label('اسم المتبرع')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('donor.blood_type')
                    ->label('فصيلة الدم')
                    ->formatStateUsing(fn($state) => Donor::getBloodTypeOptions()[$state] ?? $state)
                    ->badge()
                    ->color('danger'),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn($state) => RequestResponse::getStatusOptions()[$state] ?? $state)
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        RequestResponse::STATUS_PENDING => 'gray',
                        RequestResponse::STATUS_ACCEPTED => 'info',
                        RequestResponse::STATUS_DECLINED => 'danger',
                        RequestResponse::STATUS_COMPLETED => 'success',
                        RequestResponse::STATUS_IGNORED => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('responded_at')
                    ->label('وقت الاستجابة')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Create mostly handles implicitly via App
            ])
            ->recordActions([
                TableAction::make('accept')
                    ->label('قبول')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(fn(RequestResponse $record) => $record->update(['status' => RequestResponse::STATUS_ACCEPTED]))
                    ->visible(fn(RequestResponse $record) => $record->status === RequestResponse::STATUS_PENDING),

                TableAction::make('reject')
                    ->label('رفض')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn(RequestResponse $record) => $record->update(['status' => RequestResponse::STATUS_DECLINED]))
                    ->visible(fn(RequestResponse $record) => $record->status === RequestResponse::STATUS_PENDING),

                TableAction::make('complete')
                    ->label('إتمام التبرع')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn(RequestResponse $record) => $record->update(['status' => RequestResponse::STATUS_COMPLETED, 'verified_at' => now()]))
                    ->visible(fn(RequestResponse $record) => $record->status === RequestResponse::STATUS_ACCEPTED || $record->status === RequestResponse::STATUS_PENDING),
            ])
            ->bulkActions([
                //
            ]);
    }
}
