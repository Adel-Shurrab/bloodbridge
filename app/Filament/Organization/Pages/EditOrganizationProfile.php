<?php

namespace App\Filament\Organization\Pages;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Pages\Tenancy\EditTenantProfile;

use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use Dotswan\MapPicker\Fields\Map;

use LaraZeus\SpatieTranslatable\Resources\Concerns\HasActiveLocaleSwitcher;

class EditOrganizationProfile extends EditTenantProfile
{
    use HasActiveLocaleSwitcher;

    /**
     * Attributes managed via the locale switcher.
     */
    protected const TRANSLATABLE_ATTRIBUTES = ['org_name', 'description'];

    public ?string $activeLocale = null;

    public array $otherLocaleData = [];

    public static function getTranslatableLocales(): array
    {
        return ['ar', 'en'];
    }

    protected function fillForm(): void
    {
        $this->activeLocale ??= $this->getTranslatableLocales()[0];
        $this->tenant = \Filament\Facades\Filament::getTenant();

        $data = [];

        foreach ($this->getTranslatableLocales() as $locale) {
            $translatedData = [];
            foreach (self::TRANSLATABLE_ATTRIBUTES as $attribute) {
                $translatedData[$attribute] = $this->tenant->getTranslation($attribute, $locale, false);
            }

            if ($locale !== $this->activeLocale) {
                $this->otherLocaleData[$locale] = $translatedData;
                continue;
            }

            $data = array_merge($this->tenant->attributesToArray(), $translatedData);
        }

        $this->callHook('beforeFill');
        $data = $this->mutateFormDataBeforeFill($data);
        $this->form->fill($data);
        $this->callHook('afterFill');
    }

    public static function getLabel(): string
    {
        return __('organization.organization_profile');
    }

    public function getTitle(): string
    {
        return __('organization.manage_organization_profile');
    }

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                
                Section::make(__('organization.basic_information'))
                    ->description(__('organization.basic_data_hospital_center'))
                    ->icon('heroicon-o-building-office-2')
                    ->iconColor('primary')
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('org_name')
                                    ->label(__('organization.organization_medical_center_name'))
                                    ->placeholder(__('organization.enter_organization_name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-m-building-office')
                                    ->helperText(__('organization.official_name_of_organization'))
                                    ->live(onBlur: true)
                                    ->columnSpan(2),

                                Textarea::make('description')
                                    ->label(__('organization.organization_description'))
                                    ->placeholder(__('organization.enter_brief_description'))
                                    ->rows(4)
                                    ->maxLength(1000)
                                    ->helperText(__('organization.organization_brief_profile'))
                                    ->columnSpan(2),
                            ]),
                    ]),

                Section::make(__('organization.contact_information'))
                    ->description(__('organization.contact_details_and_location'))
                    ->icon('heroicon-o-phone')
                    ->iconColor('success')
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('contact_email')
                                    ->label(__('organization.email'))
                                    ->placeholder('example@hospital.ps')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-m-envelope')
                                    ->helperText(__('organization.official_email_for_communication'))
                                    ->suffixIcon('heroicon-m-at-symbol')
                                    ->columnSpan(1),

                                TextInput::make('contact_phone')
                                    ->label(__('organization.phone_number'))
                                    ->placeholder('970591234567')
                                    ->tel()
                                    ->required()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-m-phone')
                                    ->helperText(__('organization.landline_or_mobile_phone'))
                                    ->columnSpan(1),

                                Map::make('location')
                                    ->label(__('organization.organization_location_on_map'))
                                    ->columnSpanFull()
                                    ->defaultLocation(
                                        app(\App\Settings\GeneralSettings::class)->map_default_lat,
                                        app(\App\Settings\GeneralSettings::class)->map_default_lng
                                    )
                                    ->afterStateUpdated(function (Get $get, Set $set, ?array $state): void {
                                        $set('lat', $state['lat']);
                                        $set('lng', $state['lng']);
                                    })
                                    ->afterStateHydrated(function ($state, $record, Set $set): void {
                                        $set('location', [
                                            'lat' => $record?->lat ?? app(\App\Settings\GeneralSettings::class)->map_default_lat,
                                            'lng' => $record?->lng ?? app(\App\Settings\GeneralSettings::class)->map_default_lng
                                        ]);
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
                                    ->zoom(app(\App\Settings\GeneralSettings::class)->map_zoom_city)
                                    ->detectRetina(true)
                                    ->showMyLocationButton(true)
                                    ->extraTileControl([])
                                    ->extraControl([
                                        'zoomDelta' => 1,
                                        'zoomSnap' => 2,
                                    ]),

                                Hidden::make('lat'),
                                Hidden::make('lng'),
                            ]),
                    ]),

                Section::make(__('organization.working_hours_capacity'))
                    ->description(__('organization.daily_working_hours_center_capacity'))
                    ->icon('heroicon-o-clock')
                    ->iconColor('warning')
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        
                        Group::make()
                            ->schema([
                                Placeholder::make('working_hours_label')
                                    ->label(__('organization.working_hours'))
                                    ->content(__('organization.specify_daily_opening_and_closing')),

                                Toggle::make('emergency_available')
                                    ->label(__('organization.emergency_duty_24_7'))
                                    ->helperText(__('organization.center_operates_around_clock'))
                                    ->default(false)
                                    ->inline(false)
                                    ->live()
                                    ->afterStateHydrated(function (Toggle $component, $record) {
                                        if ($record) {
                                            $component->state($record->opening_time === null && $record->closing_time === null);
                                        }
                                    })
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            
                                            $set('opening_time', null);
                                            $set('closing_time', null);
                                        }
                                    }),

                                Grid::make(3)
                                    ->schema([
                                        TimePicker::make('opening_time')
                                            ->label(__('organization.opening_time'))
                                            ->placeholder('08:00')
                                            ->seconds(false)
                                            ->native(false)
                                            ->prefixIcon('heroicon-m-clock')
                                            ->helperText(__('organization.time_for_starting_to_receive_donors'))
                                            ->displayFormat('H:i')
                                            ->visible(fn(callable $get) => !$get('emergency_available'))
                                            ->required(fn(callable $get) => !$get('emergency_available'))
                                            ->dehydrated()
                                            ->columnSpan(1),

                                        TimePicker::make('closing_time')
                                            ->label(__('organization.closing_time'))
                                            ->placeholder('16:00')
                                            ->seconds(false)
                                            ->native(false)
                                            ->prefixIcon('heroicon-m-clock')
                                            ->helperText(__('organization.time_for_ending_donor_reception'))
                                            ->displayFormat('H:i')
                                            ->after('opening_time')
                                            ->visible(fn(callable $get) => !$get('emergency_available'))
                                            ->required(fn(callable $get) => !$get('emergency_available'))
                                            ->dehydrated()
                                            ->columnSpan(1),

                                        TextInput::make('daily_capacity')
                                            ->label(__('organization.daily_capacity'))
                                            ->placeholder('50')
                                            ->numeric()
                                            ->required()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(1000)
                                            ->suffix(__('organization.donor_per_day'))
                                            ->prefixIcon('heroicon-m-user-group')
                                            ->helperText(__('organization.number_of_donors_received_daily'))
                                            ->columnSpan(fn(callable $get) => $get('emergency_available') ? 3 : 1),
                                    ]),
                            ]),

                        Group::make()
                            ->schema([
                                Placeholder::make('working_days_label')
                                    ->label(__('organization.weekly_working_days'))
                                    ->content(__('organization.select_working_days_center_receives_donors')),

                                CheckboxList::make('working_days')
                                    ->label('')
                                    ->options(\App\Models\Organization::getWorkingDaysOptions())
                                    ->columns(4)
                                    ->gridDirection('row')
                                    ->bulkToggleable()
                                    ->helperText(__('organization.select_or_deselect_all_days'))
                                    ->columnSpanFull()
                                    ->rules([])
                                    ->dehydrated(true)
                                    ->dehydrateStateUsing(fn($state) => is_array($state) ? array_map('intval', array_values($state)) : []),
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        if ($data['emergency_available'] ?? false) {
            $data['opening_time'] = null;
            $data['closing_time'] = null;
        }
        
        // Save current locale translations
        foreach (self::TRANSLATABLE_ATTRIBUTES as $attribute) {
            if (isset($data[$attribute])) {
                $record->setTranslation($attribute, $this->activeLocale, $data[$attribute]);
            }
        }

        // Save other locales from otherLocaleData
        foreach ($this->otherLocaleData as $locale => $localeData) {
            foreach (self::TRANSLATABLE_ATTRIBUTES as $attribute) {
                if (isset($localeData[$attribute])) {
                    $record->setTranslation($attribute, $locale, $localeData[$attribute]);
                }
            }
        }

        $record->fill(\Illuminate\Support\Arr::except($data, self::TRANSLATABLE_ATTRIBUTES));
        $record->save();

        return $record;
    }

    public function updatingActiveLocale(): void
    {
        $this->otherLocaleData[$this->activeLocale] = \Illuminate\Support\Arr::only(
            $this->form->getRawState(),
            self::TRANSLATABLE_ATTRIBUTES
        );
    }

    public function updatedActiveLocale(): void
    {
        $this->form->fill([
            ...\Illuminate\Support\Arr::except(
                $this->form->getRawState(),
                self::TRANSLATABLE_ATTRIBUTES
            ),
            ...$this->otherLocaleData[$this->activeLocale] ?? [],
        ]);

        unset($this->otherLocaleData[$this->activeLocale]);
    }
}
