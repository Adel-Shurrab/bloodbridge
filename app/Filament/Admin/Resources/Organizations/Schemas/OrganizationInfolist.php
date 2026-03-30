<?php

namespace App\Filament\Admin\Resources\Organizations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrganizationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make(__('Organization Identity'))
                    ->icon('heroicon-o-building-office-2')
                    ->columns(3)
                    ->components([
                        TextEntry::make('org_name')
                            ->label(__('Organization Name'))
                            ->weight('bold')
                            ->size('lg')
                            ->getStateUsing(fn($record) => $record->localized_org_name)
                            ->columnSpan(2),

                        TextEntry::make('slug')
                            ->label(__('Slug'))
                            ->copyable()
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('license_number')
                            ->label(__('License Number'))
                            ->copyable()
                            ->icon('heroicon-o-document-text'),

                        TextEntry::make('governorate.name')
                            ->label(__('Governorate'))
                            ->badge()
                            ->color('info')
                            ->getStateUsing(fn($record) => $record->governorate?->localized_name),

                        TextEntry::make('description')
                            ->label(__('Organization Description'))
                            ->columnSpanFull()
                            ->placeholder(__('No description')),
                    ]),

                Section::make(__('Approval Status'))
                    ->icon('heroicon-o-shield-check')
                    ->columns(3)
                    ->components([

                        TextEntry::make('approval_status')
                            ->label(__('Status'))
                            ->badge()
                            ->size('lg'),

                        TextEntry::make('approved_at')
                            ->label(__('Approval Date'))
                            ->dateTime('Y/m/d')
                            ->placeholder(__('Not approved yet'))
                            ->icon('heroicon-o-check-badge'),

                        TextEntry::make('approvedBy.name')
                            ->label(__('Approved By'))
                            ->placeholder('—')
                            ->icon('heroicon-o-user'),

                        TextEntry::make('rejection_reason')
                            ->label(__('Rejection Reason'))
                            ->placeholder('—')
                            ->color('danger')
                            ->visible(fn($record) => filled($record?->rejection_reason)),

                        TextEntry::make('created_at')
                            ->label(__('Registration Date'))
                            ->dateTime('Y/m/d')
                            ->since()
                            ->icon('heroicon-o-clock'),
                    ]),

                Section::make(__('Manager'))
                    ->icon('heroicon-o-user-circle')
                    ->columns(3)
                    ->components([
                        TextEntry::make('responsible_person_name')
                            ->label(__('Name'))
                            ->weight('semibold'),

                        TextEntry::make('responsible_person_position')
                            ->label(__('Position'))
                            ->placeholder('—'),

                        TextEntry::make('responsible_person_email')
                            ->label(__('Personal Email'))
                            ->icon('heroicon-o-envelope')
                            ->url(fn($state) => $state ? 'mailto:' . $state : null)
                            ->placeholder('—'),
                    ]),

                Section::make(__('Contact Information'))
                    ->icon('heroicon-o-phone-arrow-up-right')
                    ->columns(3)
                    ->components([
                        TextEntry::make('user.email')
                            ->label(__('Official Account Email'))
                            ->icon('heroicon-o-envelope')
                            ->copyable()
                            ->url(fn($state) => $state ? 'mailto:' . $state : null),

                        TextEntry::make('contact_email')
                            ->label(__('Official Contact Email'))
                            ->icon('heroicon-o-envelope')
                            ->copyable()
                            ->url(fn($state) => $state ? 'mailto:' . $state : null),

                        TextEntry::make('contact_phone')
                            ->label(__('Contact Phone'))
                            ->copyable()
                            ->icon('heroicon-o-phone')
                            ->url(fn($state) => $state ? 'tel:' . $state : null),
                    ]),

                Section::make(__('Working Hours and Capacity'))
                    ->icon('heroicon-o-clock')
                    ->columns(4)
                    ->components([
                        TextEntry::make('opening_time')
                            ->label(__('Opening Time'))
                            ->placeholder(__('Not specified')),

                        TextEntry::make('closing_time')
                            ->label(__('Closing Time'))
                            ->placeholder(__('Not specified')),

                        TextEntry::make('daily_capacity')
                            ->label(__('Daily Capacity'))
                            ->formatStateUsing(fn($state) => $state ? $state . ' ' . __('donors/day') : '—'),

                        TextEntry::make('working_days')
                            ->label(__('Working Days'))
                            ->getStateUsing(function ($record) {
                                $names = [
                                    __('Sunday'),
                                    __('Monday'),
                                    __('Tuesday'),
                                    __('Wednesday'),
                                    __('Thursday'),
                                    __('Friday'),
                                    __('Saturday'),
                                ];
                                $raw  = $record->working_days;
                                $days = is_array($raw) ? $raw : json_decode($raw ?? '[]', true);
                                if (! is_array($days) || empty($days)) return '—';
                                return implode('، ', array_map(fn($d) => $names[(int) $d] ?? $d, $days));
                            }),
                    ]),

                Section::make(__('Statistics'))
                    ->icon('heroicon-o-chart-bar-square')
                    ->columns(3)
                    ->components([
                        TextEntry::make('total_request_created')
                            ->label(__('Total Requests Created'))
                            ->formatStateUsing(fn($state) => number_format($state) . ' ' . __('requests'))
                            ->badge()
                            ->color('primary'),

                        TextEntry::make('total_donation_verified')
                            ->label(__('Total Verified Donations'))
                            ->formatStateUsing(fn($state) => number_format($state) . ' ' . __('donations'))
                            ->badge()
                            ->color('success'),

                        TextEntry::make('bloodRequests_count')
                            ->label(__('Currently Active Requests'))
                            ->getStateUsing(fn($record) => $record->bloodRequests()->active()->count())
                            ->formatStateUsing(fn($state) => $state . ' ' . __('requests'))
                            ->badge()
                            ->color(fn($state) => $state > 0 ? 'warning' : 'gray'),
                    ]),

                Section::make(__('Geographic Location'))
                    ->icon('heroicon-o-map-pin')
                    ->collapsed()
                    ->columns(3)
                    ->components([
                        TextEntry::make('auto_location_address')
                            ->label(__('Address'))
                            ->columnSpanFull()
                            ->placeholder(__('No address')),

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
                            ->placeholder(__('No coordinates')),
                    ]),
            ]);
    }
}
