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
                Section::make(__('Basic Request Information'))
                    ->description(__('Specify blood type details and requested quantity'))
                    ->icon('heroicon-o-heart')
                    ->collapsible()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('blood_type')
                                    ->label(__('Required Blood Type'))
                                    ->options(\App\Enums\BloodType::class)
                                    ->required()
                                    ->native(false)
                                    ->placeholder(__('Select Blood Type'))
                                    ->columnSpan(1)
                                    ->searchable()
                                    ->disabled(fn($record) => $record && in_array($record->status, [
                                        BloodRequestStatus::FULFILLED,
                                        BloodRequestStatus::CANCELLED,
                                    ])),

                                TextInput::make('units_needed')
                                    ->label(__('Units Needed'))
                                    ->helperText(__('Maximum 100 units'))
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(100)
                                    ->columnSpan(1)
                                    ->suffix(__('Unit')),

                                Select::make('urgency_level')
                                    ->label(__('Urgency Level'))
                                    ->options(\App\Enums\UrgencyLevel::class)
                                    ->required()
                                    ->default(\App\Enums\UrgencyLevel::NORMAL)
                                    ->native(false)
                                    ->columnSpan(1)
                                    ->placeholder(__('Specify Urgency Level'))
                                    ->disabled(fn($record) => $record && in_array($record->status, [
                                        BloodRequestStatus::FULFILLED,
                                        BloodRequestStatus::CANCELLED,
                                    ])),
                            ]),

                        Textarea::make('additional_notes')
                            ->label(__('Additional Notes'))
                            ->placeholder(__('Add any additional details such as: surgery date, patient name, contact number...'))
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make(__('Location & Search Radius'))
                    ->description(__('Specify case location and donor search radius. Note: Use the map only if the donation location is outside the organization headquarters.'))
                    ->icon('heroicon-o-map-pin')
                    ->collapsible()
                    ->collapsed(false)
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('search_radius_km')
                                    ->label(__('Donor Search Radius'))
                                    ->helperText(__('Distance in kilometers to search for suitable donors'))
                                    ->required()
                                    ->numeric()
                                    ->default(10)
                                    ->minValue(1)
                                    ->maxValue(100)
                                    ->suffix(__('km'))
                                    ->columnSpan(2)
                                    ->disabled(fn($record) => $record && in_array($record->status, [
                                        BloodRequestStatus::FULFILLED,
                                        BloodRequestStatus::CANCELLED,
                                    ])),

                                Map::make('location')
                                    ->label(__('Case Location on Map (Optional)'))
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
                                    ->label(__('Specific Address'))
                                    ->placeholder(__('Enter address details manually (e.g., external building, second floor...)'))
                                    ->helperText(__('Please write the address in detail to help the donor reach it.'))
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }
}

