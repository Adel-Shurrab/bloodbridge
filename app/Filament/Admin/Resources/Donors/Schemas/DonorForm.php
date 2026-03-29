<?php

namespace App\Filament\Admin\Resources\Donors\Schemas;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Donor;
use App\Models\Governorate;
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
                Section::make(__('admin.user_account'))
                    ->description(__('admin.create_new_user_account_or_select_existing'))
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        Select::make('user_id')
                            ->label(__('admin.linked_account'))
                            ->relationship('user', 'name', function ($query) {
                                return $query->where('role', UserRole::DONOR)
                                    ->orderByRaw('EXISTS (SELECT 1 FROM donors WHERE donors.user_id = users.id) ASC')
                                    ->orderBy('name');
                            })
                            ->getOptionLabelFromRecordUsing(function (User $user, $record) {
                                $isOccupied = Donor::where('user_id', $user->id)
                                    ->when($record, fn($q) => $q->where('id', '!=', $record->id))
                                    ->exists();
                                return $isOccupied ? $user->name . ' (' . __('admin.occupied') . ')' : $user->name;
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
                                    ->label(__('admin.name'))
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('email')
                                    ->label(__('admin.email'))
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique('users', 'email'),

                                TextInput::make('phone')
                                    ->label(__('admin.phone'))
                                    ->tel()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique('users', 'phone'),

                                TextInput::make('password')
                                    ->label(__('admin.password'))
                                    ->password()
                                    ->revealable()
                                    ->required()
                                    ->minLength(8)
                                    ->dehydrateStateUsing(fn($state) => $state ? Hash::make($state) : null)
                                    ->hidden(fn(string $context) => $context === 'edit'),
                            ])
                            ->createOptionAction(function ($action) {
                                return $action
                                    ->modalHeading(__('admin.create_new_user'))
                                    ->modalSubmitActionLabel(__('admin.create'));
                            }),
                    ])->columns(2),

                Section::make(__('admin.personal_information'))
                    ->description(__('admin.basic_donor_data'))
                    ->icon('heroicon-o-identification')
                    ->schema([
                        TextInput::make('national_id')
                            ->label(__('admin.national_id'))
                            ->required()
                            ->maxLength(9)
                            ->minLength(9)
                            ->numeric()
                            ->unique('donors', 'national_id', ignoreRecord: true),

                        Select::make('gender')
                            ->label(__('admin.gender'))
                            ->options(\App\Enums\Gender::class)
                            ->required()
                            ->native(false),

                        DatePicker::make('birth_date')
                            ->label(__('admin.birth_date'))
                            ->required()
                            ->native(false)
                            ->maxDate(now()->subYears(app(\App\Settings\GeneralSettings::class)->min_donor_age))
                            ->minDate(now()->subYears(app(\App\Settings\GeneralSettings::class)->max_donor_age))
                            ->displayFormat('Y/m/d'),

                        Select::make('governorate_id')
                            ->label(__('admin.governorate'))
                            ->options(Governorate::localizedOptions())
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(2),

                Section::make(__('admin.geographic_location'))
                    ->description(__('admin.geographic_coordinates_of_the_donor_optional'))
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        TextInput::make('lat')
                            ->label(__('admin.latitude'))
                            ->numeric()
                            ->minValue(-90)
                            ->maxValue(90)
                            ->step(0.000001),

                        TextInput::make('lng')
                            ->label(__('admin.longitude'))
                            ->numeric()
                            ->minValue(-180)
                            ->maxValue(180)
                            ->step(0.000001),
                    ])->columns(2)
                    ->collapsed(),

                Section::make(__('admin.health_profile'))
                    ->description(__('admin.health_and_medical_information'))
                    ->icon('heroicon-o-heart')
                    ->relationship('healthProfile')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('blood_type')
                                    ->label(__('organization.blood_type'))
                                    ->options(\App\Enums\BloodType::class)
                                    ->required()
                                    ->native(false),

                                TextInput::make('weight')
                                    ->label(__('admin.weight'))
                                    ->numeric()
                                    ->minValue(app(\App\Settings\GeneralSettings::class)->min_donor_weight)
                                    ->maxValue(200)
                                    ->suffix(__('admin.kg')),

                                TextInput::make('height')
                                    ->label(__('admin.height'))
                                    ->numeric()
                                    ->minValue(120)

                                    ->minValue(app(\App\Settings\GeneralSettings::class)->min_donor_height)
                                    ->maxValue(220)
                                    ->suffix(__('admin.cm')),
                            ]),

                        Fieldset::make(__('admin.health_status'))
                            ->schema([
                                Toggle::make('recent_donation')
                                    ->label(__('admin.recent_donation'))
                                    ->default(false)
                                    ->live()
                                    ->inline(false),

                                Toggle::make('chronic_disease')
                                    ->label(__('admin.chronic_disease'))
                                    ->default(false)
                                    ->inline(false),

                                Toggle::make('infection')
                                    ->label(__('admin.infection'))
                                    ->default(false)
                                    ->inline(false),

                                Toggle::make('has_recent_surgery')
                                    ->label(__('admin.recent_surgery'))
                                    ->default(false)
                                    ->live()
                                    ->inline(false),
                            ])->columns(3),

                        Fieldset::make(__('admin.donation_and_surgery_dates'))
                            ->schema([
                                DatePicker::make('last_donation_date')
                                    ->label(__('admin.last_donation_date'))
                                    ->native(false)
                                    ->maxDate(now())
                                    ->visible(fn($get) => $get('recent_donation'))
                                    ->required(fn($get) => $get('recent_donation')),

                                DatePicker::make('surgery_date')
                                    ->label(__('admin.surgery_date'))
                                    ->native(false)
                                    ->maxDate(now())
                                    ->visible(fn($get) => $get('has_recent_surgery'))
                                    ->required(fn($get) => $get('has_recent_surgery')),

                                DatePicker::make('next_eligible_date')
                                    ->label(__('admin.next_eligible_date'))
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
