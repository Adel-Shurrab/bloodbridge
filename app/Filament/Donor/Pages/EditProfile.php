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

    // ✅ Filament v4: non-static view
    protected string $view = 'filament.donor.pages.edit-profile';

    public ?array $data = [];

    public bool $bloodTypeLocked = false;

    // ✅ Hide heading so countdown becomes first
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

        // ✅ lock ONLY blood_type if verified
        $this->bloodTypeLocked = ! is_null($healthProfile?->verified_at);

        // ✅ Location defaults (saved or Gaza)
        $lat = $donor?->lat ?? \App\Constants\PalestineCoordinates::GAZA['lat'];
        $lng = $donor?->lng ?? \App\Constants\PalestineCoordinates::GAZA['lng'];

        // ✅ Organization name that verified blood type (if any)
        $verifyingOrgName = $healthProfile?->verifyingOrganization?->org_name
            ?? $healthProfile?->verifyingOrganization?->name
            ?? null;

        $this->form->fill([
            // users
            'name' => $user?->name,
            'email' => $user?->email,
            'phone' => $user?->phone,

            // donors
            'birth_date' => $donor?->birth_date,
            'gender' => $donor?->gender?->value ?? $donor?->gender,
            'address' => $donor?->auto_location_address ?? $donor?->address,

            // ✅ coordinates
            'lat' => $lat,
            'lng' => $lng,

            // ✅ map state (marker stability)
            'location' => [
                'lat' => $lat,
                'lng' => $lng,
            ],

            // health profile (✅ Required fields + booleans)
            'weight' => $healthProfile?->weight,
            'height' => $healthProfile?->height,
            'chronic_disease' => (bool) ($healthProfile?->chronic_disease ?? false),
            'infection' => (bool) ($healthProfile?->infection ?? false),
            'has_recent_surgery' => (bool) ($healthProfile?->has_recent_surgery ?? false),
            'surgery_date' => $healthProfile?->surgery_date,

            // blood types
            'blood_type' => $healthProfile?->blood_type?->value ?? $healthProfile?->blood_type,
            'verified_blood_type' => $healthProfile?->verified_blood_type?->value ?? $healthProfile?->verified_blood_type,

            // ✅ verified by org (display only)
            'verified_by_org_name' => $verifyingOrgName,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $bloodTypeOptions = collect(BloodType::cases())
            ->mapWithKeys(fn ($case) => [
                $case->value => method_exists($case, 'getLabel') ? $case->getLabel() : $case->name,
            ])
            ->toArray();

        $genderOptions = collect(Gender::cases())
            ->mapWithKeys(fn ($case) => [
                $case->value => method_exists($case, 'getLabel') ? $case->getLabel() : $case->name,
            ])
            ->toArray();

        return $schema
            ->statePath('data')
            ->components([

                // =========================
                // Section 1: الملف الشخصي
                // =========================
                Section::make('الملف الشخصي')
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
                                    ->label('العنوان')
                                    ->maxLength(500)
                                    ->columnSpanFull(),

                                // ✅ Map Component (marker stays)
                                Map::make('location')
                                    ->label('موقعك على الخريطة')
                                    ->helperText('انقر على الخريطة أو اسحب العلامة لتحديد موقعك بدقة')
                                    ->columnSpanFull()
                                    ->defaultLocation(
                                        latitude: \App\Constants\PalestineCoordinates::GAZA['lat'],
                                        longitude: \App\Constants\PalestineCoordinates::GAZA['lng']
                                    )
                                    ->afterStateUpdated(function (Set $set, ?array $state): void {
                                        if (! is_array($state)) {
                                            return;
                                        }

                                        $lat = $state['lat'] ?? null;
                                        $lng = $state['lng'] ?? null;

                                        // ✅ keep marker after re-render
                                        if ($lat !== null && $lng !== null) {
                                            $set('location', ['lat' => $lat, 'lng' => $lng]);
                                        }

                                        $set('lat', $lat);
                                        $set('lng', $lng);
                                    })
                                    ->live()
                                    ->extraStyles([
                                        'min-height: 40vh',
                                        'border-radius: 8px',
                                    ])
                                    ->showMarker(true)
                                    ->markerColor("#ef4444")
                                    ->showFullscreenControl(true)
                                    ->showZoomControl(true)
                                    ->draggable(true)
                                    ->tilesUrl("https://tile.openstreetmap.org/{z}/{x}/{y}.png")
                                    ->zoom(\App\Constants\PalestineCoordinates::ZOOM_REGION)
                                    ->detectRetina(true)
                                    ->showMyLocationButton(true)
                                    ->clickable(true),

                                Hidden::make('lat'),
                                Hidden::make('lng'),
                            ])
                            ->columns(2),
                    ]),

                // =========================
                // Section 2: الملف الصحي
                // =========================
                Section::make('الملف الصحي')
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
                                    ->visible(fn (Get $get) => (bool) $get('has_recent_surgery'))
                                    ->required(fn (Get $get) => (bool) $get('has_recent_surgery')),
                            ])
                            ->columns(2),

                        Fieldset::make('فصيلة الدم')
                            ->schema([
                                // ✅ Donor self-declared blood type (LOCKED only if verified_at)
                                Select::make('blood_type')
                                    ->label('فصيلة الدم (حسب تصريحك)')
                                    ->options($bloodTypeOptions)
                                    ->required()
                                    ->native(false)
                                    ->disabled(fn () => $this->bloodTypeLocked)
                                    ->helperText(fn () => $this->bloodTypeLocked
                                        ? 'تم تأكيد فصيلة الدم من قبل المؤسسة، ولا يمكن تعديلها.'
                                        : 'يمكنك تعديل فصيلة الدم قبل تأكيدها من المؤسسة.'
                                    ),

                                // ✅ Hospital verified blood type (read-only)
                                Select::make('verified_blood_type')
                                    ->label('فصيلة الدم (مؤكدة من المؤسسة)')
                                    ->options($bloodTypeOptions)
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->visible(fn () => $this->bloodTypeLocked),

                                // ✅ Show which organization verified it
                                TextInput::make('verified_by_org_name')
                                    ->label('تم تأكيد فصيلة الدم بواسطة')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->visible(fn () => $this->bloodTypeLocked),
                            ])
                            ->columns(2),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = Auth::user();

        // ✅ enforce blood_type lock server-side (ONLY blood_type)
        $existingHealthProfile = $user?->donor?->healthProfile;
        if (! is_null($existingHealthProfile?->verified_at)) {
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

            // ✅ blood_type only if not locked
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
            ->title('تم حفظ البيانات بنجاح ✅')
            ->send();
    }
}
