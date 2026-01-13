<?php

namespace App\Filament\Resources\BloodRequests;

use App\Filament\Resources\BloodRequests\Pages\ManageBloodRequests;
use App\Models\BloodRequest;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BloodRequestResource extends Resource
{
    protected static ?string $model = BloodRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'المناقلات';

    protected static ?string $navigationLabel = 'طلبات الدم';

    protected static ?string $pluralModelLabel = 'طلبات الدم';

    protected static ?string $recordTitleAttribute = 'additional_notes';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('organization_id')
                    ->label('المنظمة')
                    ->disabled(),
                TextInput::make('blood_type')
                    ->label('فصيلة الدم')
                    ->formatStateUsing(fn($state) => \App\Models\Donor::getBloodTypeOptions()[$state] ?? $state)
                    ->disabled(),
                TextInput::make('units_needed')
                    ->label('الوحدات المطلوبة')
                    ->numeric(),
                TextInput::make('urgency_level')
                    ->label('مستوى الاستعجال')
                    ->formatStateUsing(fn($state) => BloodRequest::getUrgencyOptions()[$state] ?? $state)
                    ->disabled(),
                TextInput::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn($state) => BloodRequest::getStatusOptions()[$state] ?? $state)
                    ->disabled(),
                Textarea::make('additional_notes')
                    ->label('ملاحظات إضافية')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('additional_notes')
            ->columns([
                TextColumn::make('organization.org_name')
                    ->label('المنظمة')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('blood_type')
                    ->label('فصيلة الدم')
                    ->formatStateUsing(fn($state) => \App\Models\Donor::getBloodTypeOptions()[$state] ?? $state)
                    ->badge()
                    ->color('danger'),
                TextColumn::make('units_needed')
                    ->label('الوحدات')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('urgency_level')
                    ->label('الاستعجال')
                    ->formatStateUsing(fn($state) => BloodRequest::getUrgencyOptions()[$state] ?? $state)
                    ->badge()
                    ->color(fn(string $state): string => match ((int)$state) {
                        BloodRequest::URGENCY_CRITICAL => 'danger',
                        BloodRequest::URGENCY_HIGH => 'warning',
                        default => 'success',
                    })
                    ->sortable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn($state) => BloodRequest::getStatusOptions()[$state] ?? $state)
                    ->badge()
                    ->color(fn(string $state): string => match ((int)$state) {
                        BloodRequest::STATUS_FULFILLED => 'success',
                        BloodRequest::STATUS_CANCELLED, BloodRequest::STATUS_EXPIRED => 'gray',
                        default => 'warning',
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('تاريخ الطلب')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(BloodRequest::getStatusOptions()),
                \Filament\Tables\Filters\SelectFilter::make('urgency_level')
                    ->label('الاستعجال')
                    ->options(BloodRequest::getUrgencyOptions()),
            ])
            ->recordActions([
                // EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\BloodRequests\BloodRequestResource\RelationManagers\ResponsesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageBloodRequests::route('/'),
        ];
    }
}
