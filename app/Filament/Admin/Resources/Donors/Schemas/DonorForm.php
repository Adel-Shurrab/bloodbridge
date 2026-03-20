<?php

namespace App\Filament\Admin\Resources\Donors\Schemas;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Donor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\Facades\Hash;

class DonorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('User Account'))
                    ->description(__('Create New User Account or Select Existing'))
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        Select::make('user_id')
                            ->label(__('Linked Account'))
                            ->relationship('user', 'name', function ($query) {
                                return $query->where('role', UserRole::DONOR)
                                    ->orderByRaw('EXISTS (SELECT 1 FROM donors WHERE donors.user_id = users.id) ASC')
                                    ->orderBy('name');
                            })
                            ->getOptionLabelFromRecordUsing(function (User $user, $record) {
                                $isOccupied = Donor::where('user_id', $user->id)
                                    ->when($record, fn($q) => $q->where('id', '!=', $record->id))
                                    ->exists();
                                return $isOccupied ? $user->name . ' (' . __('Occupied') . ')' : $user->name;
                            })
                            ->disableOptionWhen(function (string $value, $record) {
                                return Donor::where('user_id', $value)
                                    ->when($record, fn($q) => $q->where('id', '!=', $record->id))
                                    ->exists();
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label(__('Name'))
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('email')
                                    ->label(__('Email'))
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique('users', 'email'),

                                TextInput::make('phone')
                                    ->label(__('Phone'))
                                    ->tel()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique('users', 'phone'),

                                TextInput::make('password')
                                    ->label(__('Password'))
                                    ->password()
                                    ->revealable()
                                    ->required()
                                    ->minLength(8)
                                    ->dehydrateStateUsing(fn($state) => $state ? Hash::make($state) : null)
                                    ->hidden(fn(string $context) => $context === 'edit'),
                            ])
                            ->createOptionAction(function ($action) {
                                return $action
                                    ->modalHeading(__('Create New User'))
                                    ->modalSubmitActionLabel(__('Create'));
                            }),
                    ])->columns(2),

                Section::make(__('Personal Information'))
                    ->description(__('Basic donor data'))
                    ->icon('heroicon-o-identification')
                    ->schema([
                        TextInput::make('national_id')
                            ->label(__('National ID'))
                            ->required()
                            ->maxLength(9)
                            ->minLength(9)
                            ->numeric()
                            ->unique('donors', 'national_id', ignoreRecord: true),

                        Select::make('gender')
                            ->label(__('Gender'))
                            ->options(\App\Enums\Gender::class)
                            ->required()
                            ->native(false),

                        DatePicker::make('birth_date')
                            ->label(__('Birth Date'))
                            ->required()
                            ->native(false)
                            ->maxDate(now()->subYears(app(\App\Settings\GeneralSettings::class)->min_donor_age))
                            ->minDate(now()->subYears(app(\App\Settings\GeneralSettings::class)->max_donor_age))
                            ->displayFormat('Y/m/d'),

                        Select::make('governorate_id')
                            ->label(__('Governorate'))
                            ->relationship('governorate', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(2),

                Section::make(__('Geographic Location'))
                    ->description(__('Geographic coordinates of the donor (optional)'))
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        TextInput::make('lat')
                            ->label(__('Latitude'))
                            ->numeric()
                            ->minValue(-90)
                            ->maxValue(90)
                            ->step(0.000001),

                        TextInput::make('lng')
                            ->label(__('Longitude'))
                            ->numeric()
                            ->minValue(-180)
                            ->maxValue(180)
                            ->step(0.000001),
                    ])->columns(2)
                    ->collapsed(),

                Section::make(__('Health Profile'))
                    ->description(__('Health and medical information'))
                    ->icon('heroicon-o-heart')
                    ->relationship('healthProfile')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('blood_type')
                                    ->label(__('Blood Type'))
                                    ->options(\App\Enums\BloodType::class)
                                    ->required()
                                    ->native(false),

                                TextInput::make('weight')
                                    ->label(__('Weight'))
                                    ->numeric()
                                    ->minValue(app(\App\Settings\GeneralSettings::class)->min_donor_weight)
                                    ->maxValue(200)
                                    ->suffix(__('kg')),

                                TextInput::make('height')
                                    ->label(__('Height'))
                                    ->numeric()
                                    ->minValue(120)

                                    ->minValue(app(\App\Settings\GeneralSettings::class)->min_donor_height)
                                    ->maxValue(220)
                                    ->suffix(__('cm')),
                            ]),

                        Fieldset::make(__('Health Status'))
                            ->schema([
                                Toggle::make('recent_donation')
                                    ->label(__('Recent Donation'))
                                    ->default(false)
                                    ->live()
                                    ->inline(false),

                                Toggle::make('chronic_disease')
                                    ->label(__('Chronic Disease'))
                                    ->default(false)
                                    ->inline(false),

                                Toggle::make('infection')
                                    ->label(__('Infection'))
                                    ->default(false)
                                    ->inline(false),

                                Toggle::make('has_recent_surgery')
                                    ->label(__('Recent Surgery'))
                                    ->default(false)
                                    ->live()
                                    ->inline(false),
                            ])->columns(3),

                        Fieldset::make(__('Donation and Surgery Dates'))
                            ->schema([
                                DatePicker::make('last_donation_date')
                                    ->label(__('Last Donation Date'))
                                    ->native(false)
                                    ->maxDate(now())
                                    ->visible(fn($get) => $get('recent_donation'))
                                    ->required(fn($get) => $get('recent_donation')),

                                DatePicker::make('surgery_date')
                                    ->label(__('Surgery Date'))
                                    ->native(false)
                                    ->maxDate(now())
                                    ->visible(fn($get) => $get('has_recent_surgery'))
                                    ->required(fn($get) => $get('has_recent_surgery')),

                                DatePicker::make('next_eligible_date')
                                    ->label(__('Next Eligible Date'))
                                    ->native(false)
                                    ->minDate(now())
                                    ->hidden()
                                    ->dehydrated(true),
                            ])->columns(2)
                            ->visible(fn($get) => $get('recent_donation') || $get('has_recent_surgery')),
                    ]),
            ]);
    }
}
