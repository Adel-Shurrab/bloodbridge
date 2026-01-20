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
                    ->options(\App\Enums\BloodType::class)
                    ->required(),
                TextInput::make('units_needed')
                    ->label('عدد الوحدات المطلوبة')
                    ->numeric()
                    ->required()
                    ->minValue(1),
                \Filament\Forms\Components\Select::make('urgency_level')
                    ->label('مستوى الاستعجال')
                    ->options(\App\Enums\UrgencyLevel::class)
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
                    ->badge(),
                TextColumn::make('units_needed')
                    ->label('الوحدات')
                    ->sortable(),
                TextColumn::make('urgency_level')
                    ->label('الاستعجال')
                    ->badge(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('تاريخ الطلب')
                    ->dateTime('Y/m/d h:i A')
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(\App\Enums\BloodRequestStatus::class),
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
