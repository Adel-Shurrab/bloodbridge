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
use App\Constants\PalestineCoordinates;

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
        return __('Organization Profile');
    }

    public function getTitle(): string
    {
        return __('Manage Organization Profile');
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
                
                Section::make(__('Basic Information'))
                    ->description(__('Basic data for the hospital or medical center'))
                    ->icon('heroicon-o-building-office-2')
                    ->iconColor('primary')
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('org_name')
                                    ->label(__('Organization / Medical Center Name'))
                                    ->placeholder(__('Enter organization name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-m-building-office')
                                    ->helperText(__('Official name of the organization or medical center'))
                                    ->live(onBlur: true)
                                    ->columnSpan(2),

                                Textarea::make('description')
                                    ->label(__('Organization Description'))
                                    ->placeholder(__('Enter a brief description of the organization and its services...'))
                                    ->rows(4)
                                    ->maxLength(1000)
                                    ->helperText(__('A brief profile about the organization and the services it provides (up to 1000 characters)'))
                                    ->columnSpan(2),
                            ]),
                    ]),

                Section::make(__('Contact Information'))
                    ->description(__('Contact details and geographical location of the organization'))
                    ->icon('heroicon-o-phone')
                    ->iconColor('success')
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('contact_email')
                                    ->label(__('Email'))
                                    ->placeholder('example@hospital.ps')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-m-envelope')
                                    ->helperText(__('Official email for communication'))
                                    ->suffixIcon('heroicon-m-at-symbol')
                                    ->columnSpan(1),

                                TextInput::make('contact_phone')
                                    ->label(__('Phone Number'))
                                    ->placeholder('970591234567')
                                    ->tel()
                                    ->required()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-m-phone')
                                    ->helperText(__('Landline or mobile phone number'))
                                    ->columnSpan(1),

                                Map::make('location')
                                    ->label(__('Organization Location on Map'))
                                    ->columnSpanFull()
                                    ->defaultLocation(
                                        PalestineCoordinates::GAZA['lat'],
                                        PalestineCoordinates::GAZA['lng']
                                    )
                                    ->afterStateUpdated(function (Get $get, Set $set, ?array $state): void {
                                        $set('lat', $state['lat']);
                                        $set('lng', $state['lng']);
                                    })
                                    ->afterStateHydrated(function ($state, $record, Set $set): void {
                                        $set('location', [
                                            'lat' => $record?->lat ?? PalestineCoordinates::GAZA['lat'],
                                            'lng' => $record?->lng ?? PalestineCoordinates::GAZA['lng']
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
                                    ->zoom(PalestineCoordinates::ZOOM_CITY)
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

                Section::make(__('Working Hours & Capacity'))
                    ->description(__('Daily working hours and center capacity for receiving donors'))
                    ->icon('heroicon-o-clock')
                    ->iconColor('warning')
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        
                        Group::make()
                            ->schema([
                                Placeholder::make('working_hours_label')
                                    ->label(__('Working Hours'))
                                    ->content(__('Specify daily opening and closing times')),

                                Toggle::make('emergency_available')
                                    ->label(__('24/7 Emergency Duty'))
                                    ->helperText(__('When enabled, the center operates around the clock'))
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
                                            ->label(__('Opening Time'))
                                            ->placeholder('08:00')
                                            ->seconds(false)
                                            ->native(false)
                                            ->prefixIcon('heroicon-m-clock')
                                            ->helperText(__('Time for starting to receive donors'))
                                            ->displayFormat('H:i')
                                            ->visible(fn(callable $get) => !$get('emergency_available'))
                                            ->required(fn(callable $get) => !$get('emergency_available'))
                                            ->dehydrated()
                                            ->columnSpan(1),

                                        TimePicker::make('closing_time')
                                            ->label(__('Closing Time'))
                                            ->placeholder('16:00')
                                            ->seconds(false)
                                            ->native(false)
                                            ->prefixIcon('heroicon-m-clock')
                                            ->helperText(__('Time for ending donor reception'))
                                            ->displayFormat('H:i')
                                            ->after('opening_time')
                                            ->visible(fn(callable $get) => !$get('emergency_available'))
                                            ->required(fn(callable $get) => !$get('emergency_available'))
                                            ->dehydrated()
                                            ->columnSpan(1),

                                        TextInput::make('daily_capacity')
                                            ->label(__('Daily Capacity'))
                                            ->placeholder('50')
                                            ->numeric()
                                            ->required()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(1000)
                                            ->suffix(__('donor/day'))
                                            ->prefixIcon('heroicon-m-user-group')
                                            ->helperText(__('Number of donors that can be received daily'))
                                            ->columnSpan(fn(callable $get) => $get('emergency_available') ? 3 : 1),
                                    ]),
                            ]),

                        Group::make()
                            ->schema([
                                Placeholder::make('working_days_label')
                                    ->label(__('Weekly Working Days'))
                                    ->content(__('Select the working days when the center receives donors')),

                                CheckboxList::make('working_days')
                                    ->label('')
                                    ->options(\App\Models\Organization::getWorkingDaysOptions())
                                    ->columns(4)
                                    ->gridDirection('row')
                                    ->bulkToggleable()
                                    ->helperText(__('You can select or deselect all days at once'))
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

