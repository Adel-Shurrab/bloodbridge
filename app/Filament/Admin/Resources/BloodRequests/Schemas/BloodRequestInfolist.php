<?php

namespace App\Filament\Admin\Resources\BloodRequests\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BloodRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.order_details'))
                    ->icon('heroicon-o-document-text')
                    ->columns(4)
                    ->components([
                        TextEntry::make('organization.org_name')
                            ->label(__('admin.organization'))
                            ->weight('bold')
                            ->getStateUsing(fn($record) => $record->organization
                                ? $record->organization->localized_org_name
                                : null)
                            ->url(fn($record) => route('filament.admin.resources.organizations.view', $record->organization))
                            ->openUrlInNewTab(),

                        TextEntry::make('blood_type')
                            ->label(__('organization.blood_type'))
                            ->badge()
                            ->size('lg'),

                        TextEntry::make('urgency_level')
                            ->label(__('admin.urgency_level'))
                            ->badge(),

                        TextEntry::make('status')
                            ->label(__('organization.status'))
                            ->badge()
                            ->size('lg'),

                        TextEntry::make('units_needed')
                            ->label(__('donor.units_needed'))
                            ->formatStateUsing(fn($state) => $state . ' ' . __('admin.unit')),

                        TextEntry::make('donors_accepted')
                            ->label(__('admin.donors_accepted'))
                            ->formatStateUsing(fn($state) => $state . ' ' . __('admin.donor'))
                            ->badge()
                            ->color('info'),

                        TextEntry::make('donors_completed')
                            ->label(__('admin.completed_donations'))
                            ->formatStateUsing(fn($state) => $state . ' ' . __('admin.donation'))
                            ->badge()
                            ->color('success'),

                        TextEntry::make('donors_found')
                            ->label(__('admin.total_responders'))
                            ->getStateUsing(fn($record) => $record->responses_count ?? $record->responses()->count())
                            ->formatStateUsing(fn($state) => $state . ' ' . __('admin.person'))
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('additional_notes')
                            ->label(__('admin.additional_notes'))
                            ->columnSpanFull()
                            ->placeholder(__('admin.no_notes')),
                    ]),

                Section::make(__('admin.request_timeline'))
                    ->icon('heroicon-o-clock')
                    ->columns(4)
                    ->components([
                        TextEntry::make('created_at')
                            ->label(__('admin.creation_date'))
                            ->dateTime('Y/m/d H:i')
                            ->since()
                            ->icon('heroicon-o-plus-circle'),

                        TextEntry::make('broadcasted_at')
                            ->label(__('admin.broadcast_date'))
                            ->dateTime('Y/m/d H:i')
                            ->placeholder(__('admin.not_broadcasted_yet'))
                            ->icon('heroicon-o-megaphone'),

                        TextEntry::make('fulfilled_at')
                            ->label(__('admin.completion_date'))
                            ->dateTime('Y/m/d H:i')
                            ->placeholder('-')
                            ->icon('heroicon-o-check-circle'),

                        TextEntry::make('updated_at')
                            ->label(__('admin.last_update'))
                            ->dateTime('Y/m/d H:i')
                            ->since()
                            ->icon('heroicon-o-arrow-path'),
                    ]),

                Section::make(__('admin.search_scope'))
                    ->icon('heroicon-o-signal')
                    ->columns(3)
                    ->components([
                        TextEntry::make('search_radius_km')
                            ->label(__('admin.original_search_scope'))
                            ->formatStateUsing(fn($state) => $state . ' ' . __('organization.km')),

                        TextEntry::make('actual_search_radius_km')
                            ->label(__('admin.actual_search_scope'))
                            ->formatStateUsing(fn($state) => $state ? $state . ' ' . __('organization.km') : '-')
                            ->color(fn($record) => $record->wasExpanded() ? 'warning' : null),

                        TextEntry::make('expansion_steps')
                            ->label(__('admin.expansion_times'))
                            ->getStateUsing(fn($record) => $record->expansion_steps)
                            ->formatStateUsing(fn($state) => $state > 0 ? $state . ' ' . __('admin.time') : __('admin.not_expanded'))
                            ->badge()
                            ->color(fn($record) => $record->wasExpanded() ? 'warning' : 'gray'),
                    ]),

                Section::make(__('admin.request_location'))
                    ->icon('heroicon-o-map-pin')
                    ->columns(3)
                    ->components([
                        TextEntry::make('location_address')
                            ->label(__('admin.detailed_address'))
                            ->columnSpanFull()
                            ->placeholder(__('admin.no_address')),

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
                            ->placeholder(__('admin.no_coordinates')),
                    ]),
            ]);
    }
}
