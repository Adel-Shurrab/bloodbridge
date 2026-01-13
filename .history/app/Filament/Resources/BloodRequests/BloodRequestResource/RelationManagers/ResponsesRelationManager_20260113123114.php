<?php

namespace App\Filament\Resources\BloodRequests\BloodRequestResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\RequestResponse;

class ResponsesRelationManager extends RelationManager
{
    protected static string $relationship = 'responses';

    protected static ?string $title = 'الردود من المتبرعين';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('donor_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('status')
            ->columns([
                Tables\Columns\TextColumn::make('donor.name')
                    ->label('المتبرع')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('donor.blood_type')
                    ->label('فصيلة الدم')
                    ->badge()
                    ->color('danger'),
                Tables\Columns\TextColumn::make('status')
                    ->label('حالة الرد')
                    ->formatStateUsing(fn($state) => RequestResponse::getStatusOptions()[$state] ?? $state)
                    ->badge()
                    ->color(fn(string $state): string => match ((int)$state) {
                        RequestResponse::STATUS_ACCEPTED, RequestResponse::STATUS_COMPLETED => 'success',
                        RequestResponse::STATUS_DECLINED, RequestResponse::STATUS_IGNORED => 'danger',
                        default => 'warning',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الرد')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
