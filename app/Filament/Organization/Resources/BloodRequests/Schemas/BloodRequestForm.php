<?php

namespace App\Filament\Organization\Resources\BloodRequests\Schemas;

use App\Enums\BloodRequestStatus;
use App\Models\BloodRequest;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class BloodRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
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
                                    ->searchable()
                                    ->disabled(fn($record) => $record && in_array($record->status, [
                                        BloodRequestStatus::FULFILLED,
                                        BloodRequestStatus::CANCELLED,
                                    ])),

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
                                    ->options(\App\Enums\UrgencyLevel::class)
                                    ->required()
                                    ->default(\App\Enums\UrgencyLevel::NORMAL)
                                    ->native(false)
                                    ->columnSpan(1)
                                    ->placeholder('حدد مستوى الأولوية')
                                    ->disabled(fn($record) => $record && in_array($record->status, [
                                        BloodRequestStatus::FULFILLED,
                                        BloodRequestStatus::CANCELLED,
                                    ])),
                            ]),

                        Textarea::make('additional_notes')
                            ->label('ملاحظات إضافية')
                            ->placeholder('أضف أي تفاصيل إضافية مثل: موعد العملية، اسم المريض، رقم التواصل...')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('الموقع ونطاق البحث')
                    ->description('حدد موقع الحالة ونطاق البحث عن المتبرعين. ملاحظة: استخدم الخريطة فقط إذا كان مكان التبرع خارج مقر المؤسسة.')
                    ->icon('heroicon-o-map-pin')
                    ->collapsible()
                    ->collapsed(false)
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
                                    ->columnSpan(2)
                                    ->disabled(fn($record) => $record && in_array($record->status, [
                                        BloodRequestStatus::FULFILLED,
                                        BloodRequestStatus::CANCELLED,
                                    ])),

                                Map::make('location')
                                    ->label('موقع الحالة على الخريطة (اختياري)')
                                    ->columnSpanFull()
                                    ->defaultLocation(
                                        latitude: \App\Constants\PalestineCoordinates::GAZA['lat'],
                                        longitude: \App\Constants\PalestineCoordinates::GAZA['lng']
                                    )
                                    ->afterStateUpdated(function (Get $get, Set $set, ?array $state): void {
                                        $set('lat', $state['lat'] ?? null);
                                        $set('lng', $state['lng'] ?? null);
                                    })
                                    ->afterStateHydrated(function ($state, $record, Set $set): void {
                                        $set('location', [
                                            'lat' => $record?->lat ?? \App\Constants\PalestineCoordinates::GAZA['lat'],
                                            'lng' => $record?->lng ?? \App\Constants\PalestineCoordinates::GAZA['lng']
                                        ]);
                                    })
                                    ->extraStyles([
                                        'min-height: 40vh',
                                        'border-radius: 8px'
                                    ])
                                    ->liveLocation(true, true, 5000)
                                    ->showMarker(true)
                                    ->markerColor("#ef4444")
                                    ->showFullscreenControl(true)
                                    ->showZoomControl(true)
                                    ->draggable(true)
                                    ->tilesUrl("https://tile.openstreetmap.org/{z}/{x}/{y}.png")
                                    ->zoom(\App\Constants\PalestineCoordinates::ZOOM_REGION)
                                    ->detectRetina(true)
                                    ->showMyLocationButton(true)
                                    ->clickable(true),

                                Hidden::make('lat'),
                                Hidden::make('lng'),

                                TextInput::make('location_address')
                                    ->label('العنوان المحدد')
                                    ->placeholder('أدخل تفاصيل العنوان يدوياً (مثلاً: المبنى الخارجي، الطابق الثاني...)')
                                    ->helperText('يرجى كتابة العنوان بالتفصيل لمساعدة المتبرع في الوصول.')
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }
}
