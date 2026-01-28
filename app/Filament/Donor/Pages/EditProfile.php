<?php

namespace App\Filament\Donor\Pages;

use App\Enums\BloodType;
use App\Enums\Gender;
use App\Filament\Donor\Widgets\EligibilityCountdownWidget;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EditProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-m-user-circle';
    protected static ?string $navigationLabel = 'ملفي الشخصي';
    // protected static ?string $title = 'تعديل البيانات الشخصية';
    protected static ?int $navigationSort = 2;

    // Filament v4: non-static
    protected string $view = 'filament.donor.pages.edit-profile';

    public ?array $data = [];

    public bool $bloodTypeLocked = false;
    
    public function getHeading(): string
{
    return '';
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

        // ✅ Lock editing when verified by organization/hospital
        $this->bloodTypeLocked = ! is_null($healthProfile?->verified_at);

        $this->form->fill([
            // users
            'name' => $user?->name,
            'email' => $user?->email,
            'phone' => $user?->phone,

            // donors
            'birth_date' => $donor?->birth_date,
            'gender' => $donor?->gender?->value ?? $donor?->gender,
            'address' => $donor?->auto_location_address ?? $donor?->address,

            // donor_health_profiles
            // ✅ int value (Enum-backed in DB)
            'blood_type' => $healthProfile?->blood_type?->value ?? $healthProfile?->blood_type,

            // ✅ verified blood type (read-only, shown only if verified)
            'verified_blood_type' => $healthProfile?->verified_blood_type,
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
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Fieldset::make('الملف الصحي')
                            ->schema([
                                // ✅ Donor self-declared blood type
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
                                    ->dehydrated(false) // do not submit/save from donor side
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

        // Load current health profile to enforce server-side lock
        $existingHealthProfile = $user?->donor?->healthProfile;

        // ✅ If verified, prevent changing blood_type even if request is tampered
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

            // 2) Update/Create donor
            $donor = $user->donor()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'birth_date' => $data['birth_date'],
                    'gender' => $data['gender'],

                    // ✅ choose correct column based on your DB
                    'auto_location_address' => $data['address'],
                    // If your project uses `address` instead, replace above with:
                    // 'address' => $data['address'],
                ]
            );

            // 3) Update/Create health profile (ONLY allowed fields)
            $healthUpdate = [];

            if (array_key_exists('blood_type', $data)) {
                $healthUpdate['blood_type'] = $data['blood_type'];
            }

            if (! empty($healthUpdate)) {
                $donor->healthProfile()->updateOrCreate(
                    ['donor_id' => $donor->id],
                    $healthUpdate
                );
            }
        });

        Notification::make()
            ->success()
            ->title('تم حفظ البيانات بنجاح ✅')
            ->send();
    }
}
