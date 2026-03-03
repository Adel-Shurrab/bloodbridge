<?php

namespace App\Filament\Donor\Pages;

use App\Enums\BloodType;
use App\Enums\Gender;
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

class EditProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-m-user-circle';
    protected static ?string $navigationLabel = 'ملفي الشخصي';
    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.donor.pages.edit-profile';

    public ?array $data = [];

    public bool $bloodTypeLocked = false;

    public function getHeading(): string
    {
        return '';
    }

    public function getSubheading(): ?string
    {
        return null;
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
            // users
            'name' => $user?->name,
            'email' => $user?->email,
            'phone' => $user?->phone,

            // donors
            'birth_date' => $donor?->birth_date,
            'gender' => $donor?->gender?->value ?? $donor?->gender,
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

            // blood types
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

        return Section::make('الملف الشخصي')
            ->description('قم بتحديث بياناتك الأساسية وبيانات المتبرع')
            ->schema([
                Fieldset::make('المعلومات الأساسية')
                    ->schema([
                        TextInput::make('name')
                            ->label('الاسم الكامل')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->required()
                            ->maxLength(30),
                    ])
                    ->columns(2),

                Fieldset::make('بيانات المتبرع')
                    ->schema([
                        DatePicker::make('birth_date')
                            ->label('تاريخ الميلاد')
                            ->required(),

                        Select::make('gender')
                            ->label('الجنس')
                            ->options($genderOptions)
                            ->required()
                            ->native(false),

                        TextInput::make('address')
                            ->label('عنوان السكن (يتم تعبئته من الخريطة)')
                            ->required()
                            ->columnSpanFull()
                            ->helperText('يمكنك تعديل العنوان يدوياً إذا رغبت بدقة أكبر، أو حرك الدبوس على الخريطة لتحديثه.'),

                        Map::make('location')
                            ->label('تحديد الموقع بدقة على الخريطة')
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
                                        'accept-language' => 'ar'
                                    ]);

                                    if ($response->successful()) {
                                        $nomData = $response->json();

                                        // Fallback to JS-style parsing exactly
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
                                    // Silently fail if API is down
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

        return Section::make('الملف الصحي')
            ->description('بيانات صحية تساعد على تحديد الأهلية للتبرع')
            ->schema([
                Fieldset::make('القياسات')
                    ->schema([
                        TextInput::make('weight')
                            ->label('الوزن (كغ)')
                            ->numeric()
                            ->required()
                            ->minValue(30)
                            ->maxValue(300),

                        TextInput::make('height')
                            ->label('الطول (سم)')
                            ->numeric()
                            ->required()
                            ->minValue(100)
                            ->maxValue(250),
                    ])
                    ->columns(2),

                Fieldset::make('الحالة الصحية')
                    ->schema([
                        Toggle::make('chronic_disease')
                            ->label('هل لديك مرض مزمن؟')
                            ->default(false),

                        Toggle::make('infection')
                            ->label('هل لديك عدوى نشطة حالياً؟')
                            ->default(false),

                        Toggle::make('has_recent_surgery')
                            ->label('هل أجريت عملية جراحية مؤخراً؟')
                            ->default(false)
                            ->live(),

                        DatePicker::make('surgery_date')
                            ->label('تاريخ العملية')
                            ->visible(fn(Get $get) => (bool) $get('has_recent_surgery'))
                            ->required(fn(Get $get) => (bool) $get('has_recent_surgery')),
                    ])
                    ->columns(2),

                Fieldset::make('فصيلة الدم')
                    ->schema([
                        Select::make('blood_type')
                            ->label('فصيلة الدم (حسب تصريحك)')
                            ->options($bloodTypeOptions)
                            ->required()
                            ->native(false)
                            ->disabled(fn() => $this->bloodTypeLocked)
                            ->helperText(
                                fn() => $this->bloodTypeLocked
                                    ? 'تم تأكيد فصيلة الدم من قبل المؤسسة، ولا يمكن تعديلها.'
                                    : 'يمكنك تعديل فصيلة الدم قبل تأكيدها من المؤسسة.'
                            ),

                        Select::make('verified_blood_type')
                            ->label('فصيلة الدم (مؤكدة من المؤسسة)')
                            ->options($bloodTypeOptions)
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn() => $this->bloodTypeLocked),

                        TextInput::make('verified_by_org_name')
                            ->label('تم تأكيد فصيلة الدم بواسطة')
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

            // 1) Update users
            $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
            ]);

            // 2) Update/Create donor (with location)
            $donor = $user->donor()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'birth_date' => $data['birth_date'],
                    'gender' => $data['gender'],
                    'auto_location_address' => $data['address'],
                    'lat' => $data['lat'] ?? null,
                    'lng' => $data['lng'] ?? null,
                ]
            );

            // 3) Update/Create health profile
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
            ->title('تم حفظ البيانات بنجاح')
            ->send();
    }
}
