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
                Section::make(__('organization.request_details'))
                    ->icon('heroicon-o-document-text')
                    ->columns(3)
                    ->components([
                        TextEntry::make('blood_type')
                            ->label(__('organization.blood_type'))
                            ->badge()
                            ->size('lg'),

                        TextEntry::make('urgency_level')
                            ->label(__('admin.urgency_level'))
                            ->badge()
                            ->formatStateUsing(fn($state) => method_exists($state, 'getLabel') ? $state->getLabel() : $state)
                            ->color(fn($state) => match ((int) (is_object($state) ? $state->value : $state)) {
                                4 => 'danger',
                                3 => 'warning',
                                default => 'gray',
                            }),

                        TextEntry::make('status')
                            ->label(__('organization.status'))
                            ->badge()
                            ->size('lg'),

                        TextEntry::make('units_needed')
                            ->label(__('donor.units_needed'))
                            ->formatStateUsing(fn($state) => $state . ' ' . __('organization.unit')),

                        TextEntry::make('donors_accepted')
                            ->label(__('organization.donors_accepted'))
                            ->formatStateUsing(fn($state) => $state . ' ' . __('organization.donor'))
                            ->badge()
                            ->color('info')
                            ->getStateUsing(fn($record) => $record->donors_accepted),

                        TextEntry::make('donors_completed')
                            ->label(__('organization.completed_donations'))
                            ->formatStateUsing(fn($state) => $state . ' ' . __('organization.donation'))
                            ->badge()
                            ->color('success')
                            ->getStateUsing(fn($record) => $record->donors_completed),

                        TextEntry::make('donors_found')
                            ->label(__('organization.total_responders'))
                            ->getStateUsing(fn($record) => $record->responses_count ?? $record->responses()->count())
                            ->formatStateUsing(fn($state) => $state . ' ' . __('organization.person'))
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('additional_notes')
                            ->label(__('admin.additional_notes'))
                            ->columnSpanFull()
                            ->placeholder(__('organization.no_notes')),
                    ]),

                Section::make(__('organization.request_dates'))
                    ->icon('heroicon-o-clock')
                    ->columns(4)
                    ->components([
                        TextEntry::make('created_at')
                            ->label(__('organization.created_at'))
                            ->dateTime('Y/m/d H:i')
                            ->since()
                            ->icon('heroicon-o-plus-circle'),

                        TextEntry::make('broadcasted_at')
                            ->label(__('admin.broadcast_date'))
                            ->dateTime('Y/m/d H:i')
                            ->placeholder(__('organization.not_broadcasted_yet'))
                            ->icon('heroicon-o-megaphone'),

                        TextEntry::make('fulfilled_at')
                            ->label(__('organization.fulfillment_date'))
                            ->dateTime('Y/m/d H:i')
                            ->placeholder('-')
                            ->icon('heroicon-o-check-circle'),

                        TextEntry::make('updated_at')
                            ->label(__('admin.last_update'))
                            ->dateTime('Y/m/d H:i')
                            ->since()
                            ->icon('heroicon-o-arrow-path'),
                    ]),

                Section::make(__('organization.search_radius'))
                    ->icon('heroicon-o-signal')
                    ->columns(3)
                    ->components([
                        TextEntry::make('search_radius_km')
                            ->label(__('organization.original_search_radius'))
                            ->formatStateUsing(fn($state) => $state . ' ' . __('organization.km')),

                        TextEntry::make('actual_search_radius_km')
                            ->label(__('organization.actual_search_radius'))
                            ->formatStateUsing(fn($state) => $state ? $state . ' ' . __('organization.km') : '-')
                            ->color(fn($record) => $record->wasExpanded() ? 'warning' : null)
                            ->visible(fn($record) => in_array($record->status, [
                                \App\Enums\BloodRequestStatus::BROADCASTED,
                                \App\Enums\BloodRequestStatus::FULFILLED,
                            ], true)),

                        TextEntry::make('expansion_steps')
                            ->label(__('organization.expansion_times'))
                            ->getStateUsing(fn($record) => $record->expansion_steps)
                            ->formatStateUsing(fn($state) => $state > 0 ? $state . ' ' . __('organization.time') : __('organization.not_expanded'))
                            ->badge()
                            ->color(fn($record) => $record->wasExpanded() ? 'warning' : 'gray')
                            ->visible(fn($record) => in_array($record->status, [
                                \App\Enums\BloodRequestStatus::BROADCASTED,
                                \App\Enums\BloodRequestStatus::FULFILLED,
                            ], true)),
                    ]),

                Section::make(__('organization.request_location'))
                    ->icon('heroicon-o-map-pin')
                    ->columns(3)
                    ->components([
                        TextEntry::make('location_address')
                            ->label(__('admin.detailed_address'))
                            ->columnSpanFull()
                            ->placeholder(__('organization.no_address_provided')),

                        TextEntry::make('lat')
                            ->label(__('admin.latitude'))
                            ->placeholder(__('organization.not_specified')),

                        TextEntry::make('lng')
                            ->label(__('admin.longitude'))
                            ->placeholder(__('organization.not_specified')),

                        TextEntry::make('google_maps_link')
                            ->label(__('admin.google_maps_link'))
                            ->getStateUsing(fn($record) => $record->lat ? "({$record->lat}, {$record->lng})" : null)
                            ->url(fn($record) => $record->lat ? "https://www.google.com/maps?q={$record->lat},{$record->lng}" : null, true)
                            ->placeholder(__('organization.no_coordinates_provided')),
                    ]),
            ]);
    }
}
