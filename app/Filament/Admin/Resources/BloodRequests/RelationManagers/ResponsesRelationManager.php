<?php

namespace App\Filament\Admin\Resources\BloodRequests\RelationManagers;

use App\Models\RequestResponse;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ResponsesRelationManager extends RelationManager
{
    protected static string $relationship = 'responses';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('admin.donor_responses');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('status')
                    ->label(__('organization.status'))
                    ->options(\App\Enums\RequestResponseStatus::class)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('donor.user.name')
                    ->label(__('admin.donor'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('organization.status'))
                    ->badge()
                    ->description(fn(RequestResponse $record) => $record->status === \App\Enums\RequestResponseStatus::DECLINED ? $record->decline_reason : null),
                TextColumn::make('verified_at')
                    ->label(__('admin.verified'))
                    ->dateTime('Y/m/d h:i A')
                    ->placeholder(__('admin.not_verified'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('organization.response_date'))
                    ->dateTime('Y/m/d h:i A')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('organization.status'))
                    ->options(\App\Enums\RequestResponseStatus::class),
                TernaryFilter::make('verified_at')
                    ->label(__('admin.verification_status'))
                    ->placeholder(__('admin.all'))
                    ->trueLabel(__('admin.verified'))
                    ->falseLabel(__('admin.not_verified'))
                    ->queries(
                        true: fn($query) => $query->whereNotNull('verified_at'),
                        false: fn($query) => $query->whereNull('verified_at'),
                    ),
            ])
            ->headerActions([])
            ->actions([
                ViewAction::make()->label(__('admin.view')),
            ])
            ->bulkActions([]);
    }
}
