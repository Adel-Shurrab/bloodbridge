<?php

namespace App\Filament\Organization\Resources\BloodRequests\Schemas;

use App\Models\BloodRequest;
use Dotswan\MapPicker\Fields\Map;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class BloodRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        Section::make('Basic Information')
                            ->columnSpan(2)
                            ->schema([
                                Hidden::make('organization_id')
                                    ->default(fn() => auth()->user()->organization?->id),

                                Select::make('blood_type')
                                    ->label('فصيلة الدم المطلوبة')
                                    ->options(\App\Enums\BloodType::class)
                                    ->required()
                                    ->native(false),

                                TextInput::make('units_needed')
                                    ->label('عدد الوحدات المطلوبة')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(100),

                                Select::make('urgency_level')
                                    ->label('مستوى الاستعجال')
                                    ->options(BloodRequest::getUrgencyOptions())
                                    ->required()
                                    ->default(BloodRequest::URGENCY_LOW)
                                    ->native(false),

                                Textarea::make('additional_notes')
                                    ->label('ملاحظات إضافية')
                                    ->placeholder('مثال: نحتاج متبرعين للعملية الساعة 5 مساءً...')
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Location & Settings')
                            ->columnSpan(1)
                            ->schema([
                                TextInput::make('search_radius_km')
                                    ->label('نطاق البحث (كم)')
                                    ->required()
                                    ->numeric()
                                    ->default(10)
                                    ->suffix('km'),

                                Map::make('location')
                                    ->label('موقع الحالة')
                                    ->columnSpanFull()
                                    ->defaultLocation(31.5, 34.4667) // Default to Gaza
                                    ->afterStateUpdated(function (Get $get, $state) {}),
                            ]),
                    ]),
            ]);
    }
}
