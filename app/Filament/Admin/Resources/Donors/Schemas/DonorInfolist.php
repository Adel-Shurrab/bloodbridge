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

                Section::make(__('Personal Information'))
                    ->icon('heroicon-o-user-circle')
                    ->columns(3)
                    ->components([
                        TextEntry::make('user.name')
                            ->label(__('Full Name'))
                            ->weight('bold')
                            ->size('lg')
                            ->getStateUsing(fn($record, $livewire) => $record->user ? ($record->user->name) : null),

                        TextEntry::make('national_id')
                            ->label(__('National ID'))
                            ->copyable()
                            ->copyMessage(__('Copied!'))
                            ->icon('heroicon-o-identification'),

                        TextEntry::make('gender')
                            ->label(__('Gender'))
                            ->badge(),

                        TextEntry::make('user.email')
                            ->label(__('Email'))
                            ->copyable()
                            ->icon('heroicon-o-envelope')
                            ->url(fn($state) => 'mailto:' . $state),

                        TextEntry::make('user.phone')
                            ->label(__('Phone'))
                            ->copyable()
                            ->icon('heroicon-o-phone')
                            ->url(fn($state) => 'tel:' . $state),

                        TextEntry::make('birth_date')
                            ->label(__('Birth Date'))
                            ->date('Y/m/d')
                            ->icon('heroicon-o-calendar'),

                        TextEntry::make('governorate.name')
                            ->label(__('Governorate'))
                            ->badge()
                            ->color('info')
                            ->icon('heroicon-o-map-pin')
                            ->getStateUsing(fn($record, $livewire) => $record->governorate?->getTranslation('name', $livewire?->activeLocale ?? app()->getLocale(), false) ?: $record->governorate?->getTranslation('name', 'ar', false)),

                        TextEntry::make('auto_location_address')
                            ->label(__('Address'))
                            ->columnSpan(2)
                            ->placeholder(__('No address registered')),
                    ]),

                Section::make(__('System Data'))
                    ->icon('heroicon-o-chart-bar')
                    ->columns(4)
                    ->components([
                        TextEntry::make('points')
                            ->label(__('Accumulated Points'))
                            ->formatStateUsing(fn($state) => number_format($state) . ' ' . __('points'))
                            ->badge()
                            ->color('warning')
                            ->size('lg'),

                        TextEntry::make('level')
                            ->label(__('Donor Level'))
                            ->formatStateUsing(fn($state) => __('Level') . ' ' . $state)
                            ->badge()
                            ->color('primary'),

                        TextEntry::make('created_at')
                            ->label(__('Registration Date'))
                            ->dateTime('Y/m/d')
                            ->since()
                            ->icon('heroicon-o-clock'),

                        TextEntry::make('updated_at')
                            ->label(__('Last Update'))
                            ->dateTime('Y/m/d H:i')
                            ->icon('heroicon-o-arrow-path'),
                    ]),

                Section::make(__('Health Profile'))
                    ->icon('heroicon-o-heart')
                    ->columns(4)
                    ->components([

                        TextEntry::make('healthProfile.blood_type')
                            ->label(__('Blood Type (Declared)'))
                            ->badge()
                            ->size('xl'),

                        TextEntry::make('healthProfile.verified_blood_type')
                            ->label(__('Blood Type (Verified)'))
                            ->badge()
                            ->color('success')
                            ->placeholder(__('Not verified yet')),

                        TextEntry::make('healthProfile.weight')
                            ->label(__('Weight'))
                            ->suffix(' ' . __('kg'))
                            ->formatStateUsing(fn($state) => $state ? $state . ' ' . __('kg') : '—')
                            ->color(fn($state) => $state && $state < 50 ? 'danger' : null),

                        TextEntry::make('healthProfile.height')
                            ->label(__('Height'))
                            ->formatStateUsing(fn($state) => $state ? $state . ' ' . __('cm') : '—')
                            ->color(fn($state) => $state && $state < 140 ? 'danger' : null),

                        TextEntry::make('healthProfile.is_eligible')
                            ->label(__('Donation Eligibility'))
                            ->formatStateUsing(fn($state) => $state ? '✓ ' . __('Eligible to donate') : '✗ ' . __('Not eligible currently'))
                            ->badge()
                            ->size('lg')
                            ->color(fn($state) => $state ? 'success' : 'danger'),

                        TextEntry::make('healthProfile.next_eligible_date')
                            ->label(__('Next Eligibility Date'))
                            ->date('Y/m/d')
                            ->placeholder('—')
                            ->icon('heroicon-o-calendar-days')
                            ->color('warning'),

                        TextEntry::make('healthProfile.total_donations')
                            ->label(__('Total Donations'))
                            ->formatStateUsing(fn($state) => $state . ' ' . __('donations'))

                            ->badge()
                            ->color('primary'),

                        TextEntry::make('healthProfile.last_donation_date')
                            ->label(__('Last Donation'))
                            ->date('Y/m/d')
                            ->since()
                            ->placeholder(__('None'))
                            ->icon('heroicon-o-clock'),

                        TextEntry::make('healthProfile.chronic_disease')
                            ->label(__('Chronic Disease'))
                            ->formatStateUsing(fn($state) => $state ? __('Yes') : __('No'))
                            ->badge()
                            ->color(fn($state) => $state ? 'danger' : 'success'),

                        TextEntry::make('healthProfile.infection')
                            ->label(__('Current Infection'))
                            ->formatStateUsing(fn($state) => $state ? __('Yes') : __('No'))
                            ->badge()
                            ->color(fn($state) => $state ? 'danger' : 'success'),

                        TextEntry::make('healthProfile.has_recent_surgery')
                            ->label(__('Recent Surgery'))
                            ->formatStateUsing(fn($state) => $state ? __('Yes') : __('No'))
                            ->badge()
                            ->color(fn($state) => $state ? 'warning' : 'success'),

                        TextEntry::make('healthProfile.recent_donation')
                            ->label(__('Recent Donation (< 90 days)'))
                            ->formatStateUsing(fn($state) => $state ? __('Yes') : __('No'))
                            ->badge()
                            ->color(fn($state) => $state ? 'warning' : 'success'),
                    ]),

                Section::make(__('Geographic Location'))
                    ->icon('heroicon-o-map-pin')
                    ->collapsed()
                    ->columns(3)
                    ->components([
                        TextEntry::make('governorate.name')
                            ->label(__('Governorate')),

                        TextEntry::make('lat')
                            ->label(__('Latitude'))
                            ->placeholder(__('Not specified')),

                        TextEntry::make('lng')
                            ->label(__('Longitude'))
                            ->placeholder(__('Not specified')),

                        TextEntry::make('google_maps_link')
                            ->label(__('Google Maps Link'))
                            ->columnSpanFull()
                            ->getStateUsing(fn($record) => $record->lat
                                ? "({$record->lat}, {$record->lng})"
                                : null)
                            ->url(fn($record) => $record->lat
                                ? "https://www.google.com/maps?q={$record->lat},{$record->lng}"
                                : null, true)
                            ->placeholder(__('No coordinates')),
                    ]),
            ]);
    }
}
