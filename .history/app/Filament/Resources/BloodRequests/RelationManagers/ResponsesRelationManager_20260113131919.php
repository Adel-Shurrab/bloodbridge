<?php

namespace App\Filament\Resources\BloodRequests\RelationManagers;

use App\Models\RequestResponse;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Actions\ViewAction;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ResponsesRelationManager extends RelationManager
{
    protected static string $relationship = 'responses';

    protected static ?string $title = 'استجابات المتبرعين';

    public function form(Form $form): Form
    {
        return $form
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
                        RequestResponse::STATUS_ACCEPTED, RequestResponse::STATUS_COMPLETED => 'success',
                        RequestResponse::STATUS_DECLINED, RequestResponse::STATUS_IGNORED => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('created_at')
                    ->label('تاريخ الاستجابة')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
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
