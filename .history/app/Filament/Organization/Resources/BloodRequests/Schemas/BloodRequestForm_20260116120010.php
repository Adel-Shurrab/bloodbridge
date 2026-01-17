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
            ->columns(1)
            ->components([
                Hidden::make('organization_id')
                    ->default(fn() => auth()->user()->organization?->id),

                Section::make('معلومات الطلب الأساسية')
                    ->description('حدد تفاصيل فصيلة الدم والكمية المطلوبة')
                    ->icon('heroicon-o-heart')
                    ->collapsible()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('blood_type')
                                    ->label('فصيلة الدم المطلوبة')
                                    ->options(\App\Enums\BloodType::class)
                                    ->required()
                                    ->native(false)
                                    ->placeholder('اختر فصيلة الدم')
                                    ->columnSpan(1)
                                    ->searchable(),

                                TextInput::make('units_needed')
                                    ->label('عدد الوحدات')
                                    ->helperText('الحد الأقصى 100 وحدة')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(100)
                                    ->columnSpan(1)
                                    ->suffix('وحدة'),

                                Select::make('urgency_level')
                                    ->label('درجة الاستعجال')
                                    ->options(BloodRequest::getUrgencyOptions())
                                    ->required()
                                    ->default(BloodRequest::URGENCY_LOW)
                                    ->native(false)
                                    ->columnSpan(1)
                                    ->placeholder('حدد مستوى الأولوية'),
                            ]),

                        Textarea::make('additional_notes')
                            ->label('ملاحظات إضافية')
                            ->placeholder('أضف أي تفاصيل إضافية مثل: موعد العملية، اسم المريض، رقم التواصل...')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('الموقع ونطاق البحث')
                    ->description('حدد موقع الحالة ونطاق البحث عن المتبرعين')
                    ->icon('heroicon-o-map-pin')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('search_radius_km')
                                    ->label('نطاق البحث عن المتبرعين')
                                    ->helperText('المسافة بالكيلومتر للبحث عن متبرعين مناسبين')
                                    ->required()
                                    ->numeric()
                                    ->default(10)
                                    ->minValue(1)
                                    ->maxValue(100)
                                    ->suffix('كم')
                                    ->columnSpan(1),

                                Grid::make(1)
                                    ->schema([
                                        Map::make('location')
                                            ->label('موقع الحالة على الخريطة')
                                            ->helperText('انقر على الخريطة لتحديد الموقع بدقة')
                                            ->columnSpanFull()
                                            ->defaultLocation(31.5, 34.4667)
                                            ->afterStateUpdated(function (Get $get, $state) {})
                                            ->reactive(),
                                    ])
                                    ->columnSpan(2),
                            ]),
                    ]),
            ]);
    }
}