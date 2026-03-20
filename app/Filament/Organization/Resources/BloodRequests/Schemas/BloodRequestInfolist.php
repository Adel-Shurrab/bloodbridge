<?php

namespace App\Filament\Organization\Resources\BloodRequests\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BloodRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make(__('Request Details'))
                    ->icon('heroicon-o-document-text')
                    ->columns(3)
                    ->components([

                        TextEntry::make('blood_type')
                            ->label(__('Blood Type'))
                            ->badge()
                            ->size('lg'),

                        TextEntry::make('urgency_level')
                            ->label(__('Urgency Level'))
                            ->badge()
                            ->formatStateUsing(fn($state) => method_exists($state, 'getLabel') ? $state->getLabel() : $state)
                            ->color(fn($state) => match ((int) (is_object($state) ? $state->value : $state)) {
                                4 => 'danger',
                                3 => 'warning',
                                default => 'gray',
                            }),

                        TextEntry::make('status')
                            ->label(__('Status'))
                            ->badge()
                            ->size('lg'),

                        TextEntry::make('units_needed')
                            ->label(__('Units Needed'))
                            ->formatStateUsing(fn($state) => $state . ' ' . __('Unit')),

                        TextEntry::make('donors_accepted')
                            ->label(__('Donors Accepted'))
                            ->formatStateUsing(fn($state) => $state . ' ' . __('Donor'))
                            ->badge()
                            ->color('info')
                            ->getStateUsing(fn($record) => $record->donors_accepted),

                        TextEntry::make('donors_completed')
                            ->label(__('Completed Donations'))
                            ->formatStateUsing(fn($state) => $state . ' ' . __('Donation'))
                            ->badge()
                            ->color('success')
                            ->getStateUsing(fn($record) => $record->donors_completed),

                        TextEntry::make('donors_found')
                            ->label(__('Total Responders'))
                            ->getStateUsing(fn($record) => $record->responses_count ?? $record->responses()->count())
                            ->formatStateUsing(fn($state) => $state . ' ' . __('Person'))
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('additional_notes')
                            ->label(__('Additional Notes'))
                            ->columnSpanFull()
                            ->placeholder(__('No notes')),
                    ]),

                Section::make(__('Request Dates'))
                    ->icon('heroicon-o-clock')
                    ->columns(4)
                    ->components([

                        TextEntry::make('created_at')
                            ->label(__('Created At'))
                            ->dateTime('Y/m/d H:i')
                            ->since()
                            ->icon('heroicon-o-plus-circle'),

                        TextEntry::make('broadcasted_at')
                            ->label(__('Broadcast Date'))
                            ->dateTime('Y/m/d H:i')
                            ->placeholder(__('Not broadcasted yet'))
                            ->icon('heroicon-o-megaphone'),

                        TextEntry::make('fulfilled_at')
                            ->label(__('Fulfillment Date'))
                            ->dateTime('Y/m/d H:i')
                            ->placeholder('—')
                            ->icon('heroicon-o-check-circle'),

                        TextEntry::make('updated_at')
                            ->label(__('Last Update'))
                            ->dateTime('Y/m/d H:i')
                            ->since()
                            ->icon('heroicon-o-arrow-path'),
                    ]),

                Section::make(__('Search Radius'))
                    ->icon('heroicon-o-signal')
                    ->columns(3)
                    ->components([

                        TextEntry::make('search_radius_km')
                            ->label(__('Original Search Radius'))
                            ->formatStateUsing(fn($state) => $state . ' ' . __('km')),

                        TextEntry::make('actual_search_radius_km')
                            ->label(__('Actual Search Radius'))
                            ->formatStateUsing(fn($state) => $state ? $state . ' ' . __('km') : '—')
                            ->color(fn($record) => $record->wasExpanded() ? 'warning' : null)
                            ->visible(fn($record) => $record->status === \App\Enums\BloodRequestStatus::BROADCASTED || $record->status === \App\Enums\BloodRequestStatus::FULFILLED),

                        TextEntry::make('expansion_steps')
                            ->label(__('Expansion Times'))
                            ->getStateUsing(fn($record) => $record->expansion_steps)
                            ->formatStateUsing(fn($state) => $state > 0 ? $state . ' ' . __('Time') : __('Not expanded'))
                            ->badge()
                            ->color(fn($record) => $record->wasExpanded() ? 'warning' : 'gray')
                            ->visible(fn($record) => $record->status === \App\Enums\BloodRequestStatus::BROADCASTED || $record->status === \App\Enums\BloodRequestStatus::FULFILLED),
                    ]),

                Section::make(__('Request Location'))
                    ->icon('heroicon-o-map-pin')
                    ->columns(3)
                    ->components([

                        TextEntry::make('location_address')
                            ->label(__('Detailed Address'))
                            ->columnSpanFull()
                            ->placeholder(__('No address provided')),

                        TextEntry::make('lat')
                            ->label(__('Latitude'))
                            ->placeholder(__('Not specified')),

                        TextEntry::make('lng')
                            ->label(__('Longitude'))
                            ->placeholder(__('Not specified')),

                        TextEntry::make('google_maps_link')
                            ->label(__('Google Maps Link'))
                            ->getStateUsing(fn($record) => $record->lat
                                ? "({$record->lat}, {$record->lng})"
                                : null)
                            ->url(fn($record) => $record->lat
                                ? "https://www.google.com/maps?q={$record->lat},{$record->lng}"
                                : null, true)
                            ->placeholder(__('No coordinates provided')),
                    ]),
            ]);
    }
}

