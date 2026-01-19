<?php

namespace App\Filament\Resources\Donors\RelationManagers;

use App\Models\RequestResponse;
use App\Models\BloodRequest;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ResponsesRelationManager extends RelationManager
{
    protected static string $relationship = 'responses';

    protected static ?string $title = 'سجل الاستجابات';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                TextColumn::make('bloodRequest.organization.org_name')
                    ->label('المنظمة')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('bloodRequest.urgency_level')
                    ->label('مستوى الاستعجال')
                    ->badge()
                    ->formatStateUsing(fn($state) => BloodRequest::getUrgencyOptions()[$state] ?? $state)
                    ->color(fn(string $state): string => match ((int) $state) {
                        BloodRequest::URGENCY_CRITICAL => 'danger',
                        BloodRequest::URGENCY_HIGH => 'warning',
                        default => 'success',
                    }),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn($state) => RequestResponse::getStatusOptions()[$state] ?? $state)
                    ->color(fn(string $state): string => match ((int) $state) {
                        RequestResponse::STATUS_COMPLETED => 'success',
                        RequestResponse::STATUS_ACCEPTED => 'info',
                        RequestResponse::STATUS_DECLINED, RequestResponse::STATUS_NO_SHOW, RequestResponse::STATUS_IGNORED => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('created_at')
                    ->label('تاريخ الاستجابة')
                    ->dateTime('Y/m/d h:i A')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(RequestResponse::getStatusOptions()),
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
