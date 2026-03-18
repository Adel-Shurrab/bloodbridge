<?php

namespace App\Filament\Donor\Pages;

use App\Enums\BloodType;
use App\Enums\Gender;
use App\Models\Governorate;
use App\Filament\Donor\Widgets\EligibilityCountdownWidget;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use LaraZeus\SpatieTranslatable\Resources\Concerns\HasActiveLocaleSwitcher;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;

class EditProfile extends Page implements HasForms
{
    use InteractsWithForms, HasActiveLocaleSwitcher {
        HasActiveLocaleSwitcher::getActiveFormsLocale insteadof InteractsWithForms;
        HasActiveLocaleSwitcher::getActiveActionsLocale insteadof InteractsWithForms;
        HasActiveLocaleSwitcher::getFilamentTranslatableContentDriver insteadof InteractsWithForms;
    }

    public ?string $activeLocale = null;

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.donor.pages.edit-profile';

    public ?array $data = [];

    public bool $bloodTypeLocked = false;
    
    public static function getLabel(): string
    {
        return __('My Profile');
    }

    public function getHeading(): string
    {
        return '';
    }

    public function getSubheading(): ?string
    {
        return null;
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            EligibilityCountdownWidget::class,
        ];
    }

    public function mount(): void
    {
        $user = Auth::user();
        $donor = $user?->donor;
        $healthProfile = $donor?->healthProfile;

        $this->bloodTypeLocked = ! is_null($healthProfile?->verified_blood_type);

        $this->form->fill(
            $this->getInitialFormData($user, $donor, $healthProfile)
        );
    }

    private function getInitialFormData($user, $donor, $healthProfile): array
    {
        $lat = $donor?->lat ?? \App\Constants\PalestineCoordinates::GAZA['lat'];
        $lng = $donor?->lng ?? \App\Constants\PalestineCoordinates::GAZA['lng'];

        $verifyingOrgName = $healthProfile?->verifyingOrganization?->org_name
            ?? $healthProfile?->verifyingOrganization?->name
            ?? null;

        return [
            
            'name' => $user?->name,
            'email' => $user?->email,
            'phone' => $user?->phone,

            'birth_date' => $donor?->birth_date,
            'gender' => $donor?->gender?->value ?? $donor?->gender,
            'national_id' => $donor?->national_id,
            'governorate_id' => $donor?->governorate_id,
            'address' => $donor?->auto_location_address ?? $donor?->address,

            'lat' => $lat,
            'lng' => $lng,

            'location' => [
                'lat' => $lat,
                'lng' => $lng,
            ],

            'weight' => $healthProfile?->weight,
            'height' => $healthProfile?->height,
            'chronic_disease' => (bool) ($healthProfile?->chronic_disease ?? false),
            'infection' => (bool) ($healthProfile?->infection ?? false),
            'has_recent_surgery' => (bool) ($healthProfile?->has_recent_surgery ?? false),
            'surgery_date' => $healthProfile?->surgery_date,

            'blood_type' => $healthProfile?->blood_type?->value ?? $healthProfile?->blood_type,
            'verified_blood_type' => $healthProfile?->verified_blood_type?->value ?? $healthProfile?->verified_blood_type,

            'verified_by_org_name' => $verifyingOrgName,
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                $this->getProfileSection(),
                $this->getHealthSection(),
            ]);
    }

    private function getProfileSection(): Section
    {
        $genderOptions = collect(Gender::cases())
            ->mapWithKeys(fn($case) => [
                $case->value => method_exists($case, 'getLabel') ? $case->getLabel() : $case->name,
            ])
            ->toArray();

        return Section::make(__('Personal Profile'))
            ->description(__('Update your basic information and donor data'))
            ->schema([
                Fieldset::make(__('Basic Information'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('Full Name'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label(__('Email'))
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->disabled(fn() => $this->bloodTypeLocked)
                            ->helperText(fn() => $this->bloodTypeLocked ? __('Email cannot be changed after verification.') : null),

                        TextInput::make('phone')
                            ->label(__('Phone Number'))
                            ->tel()
                            ->required()
                            ->maxLength(30),

                        TextInput::make('national_id')
                            ->label(__('National ID'))
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText(__('National ID cannot be changed.')),
                    ])
                    ->columns(2),

                Fieldset::make(__('Donor Information'))
                    ->schema([
                        DatePicker::make('birth_date')
                            ->label(__('Date of Birth'))
                            ->required()
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText(__('Date of birth cannot be changed.')),

                        Select::make('gender')
                            ->label(__('Gender'))
                            ->options($genderOptions)
                            ->required()
                            ->native(false)
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText(__('Gender cannot be changed.')),

                        Select::make('governorate_id')
                            ->label(__('Governorate'))
                            ->options(Governorate::all()->pluck('name', 'id')->toArray())
                            ->required()
                            ->native(false)
                            ->searchable(),

                        TextInput::make('address')
                            ->label(__('Residential Address (filled from map)'))
                            ->required()
                            ->columnSpanFull()
                            ->helperText(__('You can manually edit the address, or move the pin on the map to update it.')),

                        Map::make('location')
                            ->label(__('Set location precisely on the map'))
                            ->columnSpanFull()
                            ->defaultLocation(
                                \App\Constants\PalestineCoordinates::GAZA['lat'],
                                \App\Constants\PalestineCoordinates::GAZA['lng']
                            )
                            ->afterStateUpdated(function (Get $get, Set $set, ?array $state): void {
                                if (!$state) return;

                                $lat = round((float)$state['lat'], 6);
                                $lng = round((float)$state['lng'], 6);

                                $set('lat', $lat);
                                $set('lng', $lng);

                                try {
                                    $response = \Illuminate\Support\Facades\Http::withHeaders([
                                        'User-Agent' => 'BloodBridge/1.0',
                                    ])->get("https://nominatim.openstreetmap.org/reverse", [
                                        'format' => 'json',
                                        'lat' => $lat,
                                        'lon' => $lng,
                                        'accept-language' => app()->getLocale()
                                    ]);

                                    if ($response->successful()) {
                                        $nomData = $response->json();

                                        $address = $nomData['address'] ?? [];
                                        $addressParts = array_filter([
                                            $address['road'] ?? $address['neighbourhood'] ?? null,
                                            $address['suburb'] ?? $address['city_district'] ?? null,
                                            $address['city'] ?? $address['town'] ?? $address['village'] ?? null,
                                            $address['state'] ?? null
                                        ]);

                                        $fullAddress = !empty($addressParts)
                                            ? implode('، ', $addressParts)
                                            : ($nomData['display_name'] ?? '');

                                        $set('address', $fullAddress);
                                    }
                                } catch (\Exception $e) {
                                    
                                }
                            })
                            ->extraStyles([
                                'min-height: 50vh',
                                'border-radius: 10px'
                            ])
                            ->liveLocation(true, true, 10000)
                            ->showMarker(true)
                            ->markerColor("#be123cff")
                            ->showFullscreenControl(true)
                            ->showZoomControl(true)
                            ->draggable(true)
                            ->tilesUrl("https://tile.openstreetmap.org/{z}/{x}/{y}.png")
                            ->zoom(\App\Constants\PalestineCoordinates::ZOOM_CITY)
                            ->detectRetina(true)
                            ->showMyLocationButton(true)
                            ->extraControl([
                                'zoomDelta' => 1,
                                'zoomSnap' => 2,
                            ]),

                        Hidden::make('lat'),
                        Hidden::make('lng'),
                    ])
                    ->columns(2),
            ]);
    }

    private function getHealthSection(): Section
    {
        $bloodTypeOptions = collect(BloodType::cases())
            ->mapWithKeys(fn($case) => [
                $case->value => method_exists($case, 'getLabel') ? $case->getLabel() : $case->name,
            ])
            ->toArray();

        return Section::make(__('Health Profile'))
            ->description(__('Health data that helps determine donation eligibility'))
            ->schema([
                Fieldset::make(__('Measurements'))
                    ->schema([
                        TextInput::make('weight')
                            ->label(__('Weight (kg)'))
                            ->numeric()
                            ->required()
                            ->minValue(30)
                            ->maxValue(300),

                        TextInput::make('height')
                            ->label(__('Height (cm)'))
                            ->numeric()
                            ->required()
                            ->minValue(100)
                            ->maxValue(250),
                    ])
                    ->columns(2),

                Fieldset::make(__('Health Status'))
                    ->schema([
                        Toggle::make('chronic_disease')
                            ->label(__('Do you have a chronic disease?'))
                            ->default(false),

                        Toggle::make('infection')
                            ->label(__('Do you have an active infection?'))
                            ->default(false),

                        Toggle::make('has_recent_surgery')
                            ->label(__('Have you had surgery recently?'))
                            ->default(false)
                            ->live(),

                        DatePicker::make('surgery_date')
                            ->label(__('Surgery Date'))
                            ->visible(fn(Get $get) => (bool) $get('has_recent_surgery'))
                            ->required(fn(Get $get) => (bool) $get('has_recent_surgery')),
                    ])
                    ->columns(2),

                Fieldset::make(__('Blood Type'))
                    ->schema([
                        Select::make('blood_type')
                            ->label(__('Blood Type (self-reported)'))
                            ->options($bloodTypeOptions)
                            ->required()
                            ->native(false)
                            ->disabled(fn() => $this->bloodTypeLocked)
                            ->helperText(
                                fn() => $this->bloodTypeLocked
                                    ? __('Blood type has been verified by the organization and cannot be changed.')
                                    : __('You can change blood type before it is verified by the organization.')
                            ),

                        Select::make('verified_blood_type')
                            ->label(__('Blood Type (verified by organization)'))
                            ->options($bloodTypeOptions)
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn() => $this->bloodTypeLocked),

                        TextInput::make('verified_by_org_name')
                            ->label(__('Blood type verified by'))
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn() => $this->bloodTypeLocked),
                    ])
                    ->columns(2),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = Auth::user();

        $existingHealthProfile = $user?->donor?->healthProfile;
        if (! is_null($existingHealthProfile?->verified_blood_type)) {
            unset($data['blood_type']);
        }

        DB::transaction(function () use ($data, $user) {
            $userUpdate = [
                'name' => $data['name'],
                'phone' => $data['phone'],
            ];

            if (isset($data['email'])) {
                $userUpdate['email'] = $data['email'];
            }

            $user->update($userUpdate);

            $donorUpdate = [
                'governorate_id' => $data['governorate_id'],
                'auto_location_address' => $data['address'],
                'lat' => $data['lat'] ?? null,
                'lng' => $data['lng'] ?? null,
            ];

            if (isset($data['birth_date'])) {
                $donorUpdate['birth_date'] = $data['birth_date'];
            }

            if (isset($data['gender'])) {
                $donorUpdate['gender'] = $data['gender'];
            }

            $donor = $user->donor()->updateOrCreate(
                ['user_id' => $user->id],
                $donorUpdate
            );

            $healthUpdate = [
                'weight' => (int) $data['weight'],
                'height' => (int) $data['height'],
                'chronic_disease' => (bool) ($data['chronic_disease'] ?? false),
                'infection' => (bool) ($data['infection'] ?? false),
                'has_recent_surgery' => (bool) ($data['has_recent_surgery'] ?? false),
                'surgery_date' => ((bool) ($data['has_recent_surgery'] ?? false)) ? ($data['surgery_date'] ?? null) : null,
            ];

            if (array_key_exists('blood_type', $data)) {
                $healthUpdate['blood_type'] = $data['blood_type'];
            }

            $donor->healthProfile()->updateOrCreate(
                ['donor_id' => $donor->id],
                $healthUpdate
            );
        });

        Notification::make()
            ->success()
            ->title(__('Profile updated successfully'))
            ->send();
    }
}

