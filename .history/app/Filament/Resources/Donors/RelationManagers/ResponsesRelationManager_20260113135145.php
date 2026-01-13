<?php

namespace App\Filament\Resources\Donors\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ResponsesRelationManager extends RelationManager
{
    protected static string $relationship = 'responses';

    protected static ?string $title = 'سجل الاستجابات';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('status')
                    ->label('الحالة')
                    ->options(\App\Models\RequestResponse::getStatusOptions())
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('bloodRequest.organization.org_name')
                    ->label('المنظمة')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('bloodRequest.urgency_level')
                    ->label('مستوى الاستعجال')
                    ->badge()
                    ->formatStateUsing(fn($state) => \App\Models\BloodRequest::getUrgencyOptions()[$state] ?? $state)
                    ->color(fn(string $state): string => match ((int) $state) {
                        \App\Models\BloodRequest::URGENCY_CRITICAL => 'danger',
                        \App\Models\BloodRequest::URGENCY_HIGH => 'warning',
                        default => 'info',
                    }),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn($state) => \App\Models\RequestResponse::getStatusOptions()[$state] ?? $state)
                    ->color(fn(string $state): string => match ((int) $state) {
                        \App\Models\RequestResponse::STATUS_ACCEPTED, \App\Models\RequestResponse::STATUS_COMPLETED => 'success',
                        \App\Models\RequestResponse::STATUS_DECLINED, \App\Models\RequestResponse::STATUS_IGNORED => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('created_at')
                    ->label('تاريخ الاستجابة')
                    ->dateTime('Y/m/d h:i A')
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(\App\Models\RequestResponse::getStatusOptions()),
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }
}
