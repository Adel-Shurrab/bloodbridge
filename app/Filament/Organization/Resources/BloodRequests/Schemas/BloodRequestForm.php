<?php

namespace App\Filament\Organization\Resources\BloodRequests\Schemas;

use App\Enums\BloodRequestStatus;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
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
                Section::make(__('organization.basic_request_information'))
                    ->description(__('organization.specify_blood_type_details_and_quantity'))
                    ->icon('heroicon-o-heart')
                    ->collapsible()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('blood_type')
                                    ->label(__('donor.required_blood_type'))
                                    ->options(\App\Enums\BloodType::class)
                                    ->required()
                                    ->native(false)
                                    ->placeholder(__('organization.select_blood_type'))
                                    ->columnSpan(1)
                                    ->searchable()
                                    ->disabled(fn($record) => $record && in_array($record->status, [
                                        BloodRequestStatus::FULFILLED,
                                        BloodRequestStatus::CANCELLED,
                                    ])),

                                TextInput::make('units_needed')
                                    ->label(__('donor.units_needed'))
                                    ->helperText(__('organization.maximum_100_units'))
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(100)
                                    ->columnSpan(1)
                                    ->suffix(__('organization.unit')),

                                Select::make('urgency_level')
                                    ->label(__('admin.urgency_level'))
                                    ->options(\App\Enums\UrgencyLevel::class)
                                    ->required()
                                    ->default(\App\Enums\UrgencyLevel::NORMAL)
                                    ->native(false)
                                    ->columnSpan(1)
                                    ->placeholder(__('organization.specify_urgency_level'))
                                    ->disabled(fn($record) => $record && in_array($record->status, [
                                        BloodRequestStatus::FULFILLED,
                                        BloodRequestStatus::CANCELLED,
                                    ])),
                            ]),

                        Textarea::make('additional_notes')
                            ->label(__('admin.additional_notes'))
                            ->placeholder(__('organization.additional_notes_placeholder'))
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make(__('organization.location_and_search_radius'))
                    ->description(__('organization.location_and_search_radius_description'))
                    ->icon('heroicon-o-map-pin')
                    ->collapsible()
                    ->collapsed(false)
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('search_radius_km')
                                    ->label(__('organization.donor_search_radius'))
                                    ->helperText(__('organization.distance_in_kilometers_for_donor_search'))
                                    ->required()
                                    ->numeric()
                                    ->default(10)
                                    ->minValue(1)
                                    ->maxValue(100)
                                    ->suffix(__('organization.km'))
                                    ->columnSpan(2)
                                    ->disabled(fn($record) => $record && in_array($record->status, [
                                        BloodRequestStatus::FULFILLED,
                                        BloodRequestStatus::CANCELLED,
                                    ])),

                                Map::make('location')
                                    ->label(__('organization.case_location_on_map_optional'))
                                    ->columnSpanFull()
                                    ->defaultLocation(
                                        latitude: app(\App\Settings\GeneralSettings::class)->map_default_lat,
                                        longitude: app(\App\Settings\GeneralSettings::class)->map_default_lng,
                                    )
                                    ->afterStateUpdated(function (Get $get, Set $set, ?array $state): void {
                                        $set('lat', $state['lat'] ?? null);
                                        $set('lng', $state['lng'] ?? null);
                                    })
                                    ->afterStateHydrated(function ($state, $record, Set $set): void {
                                        $set('location', [
                                            'lat' => $record?->lat ?? app(\App\Settings\GeneralSettings::class)->map_default_lat,
                                            'lng' => $record?->lng ?? app(\App\Settings\GeneralSettings::class)->map_default_lng,
                                        ]);
                                    })
                                    ->extraStyles([
                                        'min-height: 40vh',
                                        'border-radius: 8px',
                                    ])
                                    ->liveLocation(true, true, 5000)
                                    ->showMarker(true)
                                    ->markerColor('#ef4444')
                                    ->showFullscreenControl(true)
                                    ->showZoomControl(true)
                                    ->draggable(true)
                                    ->tilesUrl('https://tile.openstreetmap.org/{z}/{x}/{y}.png')
                                    ->zoom(app(\App\Settings\GeneralSettings::class)->map_zoom_region)
                                    ->detectRetina(true)
                                    ->showMyLocationButton(true)
                                    ->clickable(true),

                                Hidden::make('lat'),
                                Hidden::make('lng'),

                                TextInput::make('location_address')
                                    ->label(__('admin.detailed_address'))
                                    ->placeholder(__('organization.enter_address_details_manually'))
                                    ->helperText(__('organization.write_address_in_detail'))
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }
}
