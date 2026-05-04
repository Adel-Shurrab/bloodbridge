<?php

namespace App\Filament\Admin\Resources\Donors\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;

class DonorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make(__('admin.personal_information'))
                    ->icon('heroicon-o-user-circle')
                    ->columns(3)
                    ->components([
                        TextEntry::make('user.name')
                            ->label(__('admin.full_name'))
                            ->weight('bold')
                            ->size('lg')
                            ->getStateUsing(fn($record, $livewire) => $record->user ? ($record->user->name) : null),

                        TextEntry::make('national_id')
                            ->label(__('admin.national_id'))
                            ->copyable()
                            ->copyMessage(__('admin.copied'))
                            ->icon('heroicon-o-identification'),

                        TextEntry::make('gender')
                            ->label(__('admin.gender'))
                            ->badge(),

                        TextEntry::make('user.email')
                            ->label(__('admin.email'))
                            ->copyable()
                            ->icon('heroicon-o-envelope')
                            ->url(fn($state) => 'mailto:' . $state),

                        TextEntry::make('user.phone')
                            ->label(__('admin.phone'))
                            ->copyable()
                            ->icon('heroicon-o-phone')
                            ->url(fn($state) => 'tel:' . $state),

                        TextEntry::make('birth_date')
                            ->label(__('admin.birth_date'))
                            ->date('Y/m/d')
                            ->icon('heroicon-o-calendar'),

                        TextEntry::make('governorate.name')
                            ->label(__('admin.governorate'))
                            ->badge()
                            ->color('info')
                            ->icon('heroicon-o-map-pin')
                            ->getStateUsing(fn($record, $livewire) => $record->governorate?->localized_name),

                        TextEntry::make('auto_location_address')
                            ->label(__('admin.address'))
                            ->columnSpan(2)
                            ->placeholder(__('admin.no_address_registered')),
                    ]),

                Section::make(__('admin.system_data'))
                    ->icon('heroicon-o-chart-bar')
                    ->columns(4)
                    ->components([
                        TextEntry::make('points')
                            ->label(__('admin.accumulated_points'))
                            ->formatStateUsing(fn($state) => number_format($state) . ' ' . __('admin.points'))
                            ->badge()
                            ->color('warning')
                            ->size('lg'),

                        TextEntry::make('level')
                            ->label(__('admin.donor_level'))
                            ->formatStateUsing(fn($state) => __('admin.level') . ' ' . $state)
                            ->badge()
                            ->color('primary'),

                        TextEntry::make('created_at')
                            ->label(__('admin.registration_date'))
                            ->dateTime('Y/m/d')
                            ->since()
                            ->icon('heroicon-o-clock'),

                        TextEntry::make('updated_at')
                            ->label(__('admin.last_update'))
                            ->dateTime('Y/m/d H:i')
                            ->icon('heroicon-o-arrow-path'),
                    ]),

                Section::make(__('admin.health_profile'))
                    ->icon('heroicon-o-heart')
                    ->columns(4)
                    ->components([

                        TextEntry::make('healthProfile.blood_type')
                            ->label(__('admin.blood_type_declared'))
                            ->badge()
                            ->size('xl'),

                        TextEntry::make('healthProfile.verified_blood_type')
                            ->label(__('admin.blood_type_verified'))
                            ->badge()
                            ->color('success')
                            ->placeholder(__('admin.not_verified_yet')),

                        TextEntry::make('healthProfile.weight')
                            ->label(__('admin.weight'))
                            ->suffix(' ' . __('admin.kg'))
                            ->formatStateUsing(fn($state) => $state ? $state . ' ' . __('admin.kg') : '-')
                            ->color(fn($state) => $state && $state < 50 ? 'danger' : null),

                        TextEntry::make('healthProfile.height')
                            ->label(__('admin.height'))
                            ->formatStateUsing(fn($state) => $state ? $state . ' ' . __('admin.cm') : '-')
                            ->color(fn($state) => $state && $state < 140 ? 'danger' : null),

                        TextEntry::make('healthProfile.is_eligible')
                            ->label(__('admin.donation_eligibility'))
                            ->formatStateUsing(fn($state) => $state ? __('admin.eligible_to_donate') : __('admin.not_eligible_currently'))
                            ->badge()
                            ->size('lg')
                            ->color(fn($state) => $state ? 'success' : 'danger'),

                        TextEntry::make('healthProfile.next_eligible_date')
                            ->label(__('admin.next_eligibility_date'))
                            ->date('Y/m/d')
                            ->placeholder('-')
                            ->icon('heroicon-o-calendar-days')
                            ->color('warning'),

                        TextEntry::make('healthProfile.total_donations')
                            ->label(__('admin.total_donations'))
                            ->formatStateUsing(fn($state) => $state . ' ' . __('admin.donations'))

                            ->badge()
                            ->color('primary'),

                        TextEntry::make('healthProfile.last_donation_date')
                            ->label(__('admin.last_donation'))
                            ->date('Y/m/d')
                            ->since()
                            ->placeholder(__('organization.none'))
                            ->icon('heroicon-o-clock'),

                        TextEntry::make('healthProfile.chronic_disease')
                            ->label(__('admin.chronic_disease'))
                            ->formatStateUsing(fn($state) => $state ? __('admin.yes') : __('admin.no'))
                            ->badge()
                            ->color(fn($state) => $state ? 'danger' : 'success'),

                        TextEntry::make('healthProfile.infection')
                            ->label(__('admin.current_infection'))
                            ->formatStateUsing(fn($state) => $state ? __('admin.yes') : __('admin.no'))
                            ->badge()
                            ->color(fn($state) => $state ? 'danger' : 'success'),

                        TextEntry::make('healthProfile.has_recent_surgery')
                            ->label(__('admin.recent_surgery'))
                            ->formatStateUsing(fn($state) => $state ? __('admin.yes') : __('admin.no'))
                            ->badge()
                            ->color(fn($state) => $state ? 'warning' : 'success'),

                        TextEntry::make('healthProfile.recent_donation')
                            ->label(__('admin.recent_donation_under_90_days'))
                            ->formatStateUsing(fn($state) => $state ? __('admin.yes') : __('admin.no'))
                            ->badge()
                            ->color(fn($state) => $state ? 'warning' : 'success'),
                    ]),

                Section::make(__('admin.geographic_location'))
                    ->icon('heroicon-o-map-pin')
                    ->collapsed()
                    ->columns(3)
                    ->components([
                        TextEntry::make('governorate.name')
                            ->label(__('admin.governorate'))
                            ->getStateUsing(fn($record) => $record->governorate?->localized_name),

                        TextEntry::make('lat')
                            ->label(__('admin.latitude'))
                            ->placeholder(__('admin.not_specified')),

                        TextEntry::make('lng')
                            ->label(__('admin.longitude'))
                            ->placeholder(__('admin.not_specified')),

                        TextEntry::make('google_maps_link')
                            ->label(__('admin.google_maps_link'))
                            ->columnSpanFull()
                            ->getStateUsing(fn($record) => $record->lat
                                ? "({$record->lat}, {$record->lng})"
                                : null)
                            ->url(fn($record) => $record->lat
                                ? "https://www.google.com/maps?q={$record->lat},{$record->lng}"
                                : null, true)
                            ->placeholder(__('admin.no_coordinates')),
                    ]),
            ]);
    }
}
