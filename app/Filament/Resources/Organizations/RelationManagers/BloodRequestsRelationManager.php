<?php

namespace App\Filament\Resources\Organizations\RelationManagers;

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

class BloodRequestsRelationManager extends RelationManager
{
    protected static string $relationship = 'bloodRequests';

    protected static ?string $title = 'طلبات الدم';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('blood_type')
                    ->label('فصيلة الدم')
                    ->options(\App\Models\Donor::getBloodTypeOptions())
                    ->required(),
                TextInput::make('units_needed')
                    ->label('عدد الوحدات المطلوبة')
                    ->numeric()
                    ->required()
                    ->minValue(1),
                \Filament\Forms\Components\Select::make('urgency_level')
                    ->label('مستوى الاستعجال')
                    ->options(\App\Models\BloodRequest::getUrgencyOptions())
                    ->required(),
                \Filament\Forms\Components\Textarea::make('additional_notes')
                    ->label('ملاحظات إضافية')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('blood_type')
                    ->label('فصيلة الدم')
                    ->badge()
                    ->formatStateUsing(fn($state) => \App\Models\Donor::getBloodTypeOptions()[$state] ?? $state),
                TextColumn::make('units_needed')
                    ->label('الوحدات')
                    ->sortable(),
                TextColumn::make('urgency_level')
                    ->label('الاستعجال')
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
                    ->formatStateUsing(fn($state) => \App\Models\BloodRequest::getStatusOptions()[$state] ?? $state)
                    ->color(fn(string $state): string => match ((int) $state) {
                        \App\Models\BloodRequest::STATUS_FULFILLED => 'success',
                        \App\Models\BloodRequest::STATUS_CANCELLED => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('created_at')
                    ->label('تاريخ الطلب')
                    ->dateTime('Y/m/d h:i A')
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(\App\Models\BloodRequest::getStatusOptions()),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('إنشاء طلب جديد'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }
}
