<?php

namespace App\Filament\Resources\Donors\Schemas;

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

class DonorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('حساب المستخدم')
                    ->description('إنشاء حساب مستخدم جديد أو اختيار حساب موجود')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        Radio::make('user_creation_mode')
                            ->label('طريقة إضافة المستخدم')
                            ->options([
                                'create' => 'إنشاء مستخدم جديد',
                                'select' => 'اختيار مستخدم موجود',
                            ])
                            ->default('create')
                            ->inline()
                            ->live()
                            ->required()
                            ->columnSpanFull(),

                        // New User Creation Fields
                        TextInput::make('new_user_name')
                            ->label('اسم المستخدم')
                            ->required(fn($get) => $get('user_creation_mode') === 'create')
                            ->maxLength(255)
                            ->visible(fn($get) => $get('user_creation_mode') === 'create'),

                        TextInput::make('new_user_email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->required(fn($get) => $get('user_creation_mode') === 'create')
                            ->maxLength(255)
                            ->unique('users', 'email', ignoreRecord: true)
                            ->visible(fn($get) => $get('user_creation_mode') === 'create'),

                        TextInput::make('new_user_phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->required(fn($get) => $get('user_creation_mode') === 'create')
                            ->maxLength(255)
                            ->unique('users', 'phone', ignoreRecord: true)
                            ->visible(fn($get) => $get('user_creation_mode') === 'create'),

                        TextInput::make('new_user_password')
                            ->label('كلمة المرور')
                            ->password()
                            ->revealable()
                            ->required(fn($get) => $get('user_creation_mode') === 'create')
                            ->minLength(8)
                            ->visible(fn($get) => $get('user_creation_mode') === 'create'),

                        // Existing User Selection
                        Select::make('user_id')
                            ->label('الحساب المرتبط')
                            ->relationship('user', 'name', function ($query) {
                                return $query->where('role', User::ROLE_DONOR)
                                    ->orderByRaw('EXISTS (SELECT 1 FROM donors WHERE donors.user_id = users.id) ASC')
                                    ->orderBy('name');
                            })
                            ->getOptionLabelFromRecordUsing(function (User $user, $record) {
                                $isOccupied = Donor::where('user_id', $user->id)
                                    ->when($record, fn($q) => $q->where('id', '!=', $record->id))
                                    ->exists();
                                return $isOccupied ? "{$user->name} (مشغول)" : $user->name;
                            })
                            ->disableOptionWhen(function (string $value, $record) {
                                return Donor::where('user_id', $value)
                                    ->when($record, fn($q) => $q->where('id', '!=', $record->id))
                                    ->exists();
                            })
                            ->required(fn($get) => $get('user_creation_mode') === 'select')
                            ->searchable()
                            ->preload()
                            ->visible(fn($get) => $get('user_creation_mode') === 'select'),
                    ])->columns(2),

                Section::make('المعلومات الشخصية')
                    ->description('البيانات الأساسية للمتبرع')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        TextInput::make('national_id')
                            ->label('رقم الهوية الوطنية')
                            ->required()
                            ->maxLength(9)
                            ->minLength(9)
                            ->numeric()
                            ->unique('donors', 'national_id', ignoreRecord: true),

                        Select::make('gender')
                            ->label('الجنس')
                            ->options(Donor::getGenderOptions())
                            ->required()
                            ->native(false),

                        DatePicker::make('birth_date')
                            ->label('تاريخ الميلاد')
                            ->required()
                            ->native(false)
                            ->maxDate(now()->subYears(18))
                            ->displayFormat('Y/m/d'),

                        Select::make('governorate_id')
                            ->label('المحافظة')
                            ->relationship('governorate', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(2),

                Section::make('الموقع الجغرافي')
                    ->description('الإحداثيات الجغرافية للمتبرع (اختياري)')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        TextInput::make('lat')
                            ->label('خط العرض (Latitude)')
                            ->numeric()
                            ->minValue(-90)
                            ->maxValue(90)
                            ->step(0.000001),

                        TextInput::make('lng')
                            ->label('خط الطول (Longitude)')
                            ->numeric()
                            ->minValue(-180)
                            ->maxValue(180)
                            ->step(0.000001),
                    ])->columns(2)
                    ->collapsed(),

                Section::make('الملف الصحي')
                    ->description('المعلومات الصحية والطبية')
                    ->icon('heroicon-o-heart')
                    ->relationship('healthProfile')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('blood_type')
                                    ->label('فصيلة الدم')
                                    ->options(Donor::getBloodTypeOptions())
                                    ->required()
                                    ->native(false),

                                TextInput::make('weight')
                                    ->label('الوزن (كجم)')
                                    ->numeric()
                                    ->minValue(50)
                                    ->maxValue(200)
                                    ->suffix('كجم'),

                                TextInput::make('height')
                                    ->label('الطول (سم)')
                                    ->numeric()
                                    ->minValue(140)
                                    ->maxValue(250)
                                    ->suffix('سم'),
                            ]),

                        Fieldset::make('الحالة الصحية')
                            ->schema([
                                Toggle::make('chronic_disease')
                                    ->label('لديه أمراض مزمنة')
                                    ->default(false)
                                    ->inline(false),

                                Toggle::make('recent_donation')
                                    ->label('تبرع حديثاً')
                                    ->default(false)
                                    ->inline(false),

                                Toggle::make('infection')
                                    ->label('لديه عدوى')
                                    ->default(false)
                                    ->inline(false),


                                Toggle::make('has_recent_surgery')
                                    ->label('خضع لعملية جراحية مؤخراً')
                                    ->default(false)
                                    ->live()
                                    ->inline(false),

                                DatePicker::make('surgery_date')
                                    ->label('تاريخ العملية الجراحية')
                                    ->native(false)
                                    ->maxDate(now())
                                    ->visible(fn($get) => $get('has_recent_surgery'))
                                    ->required(fn($get) => $get('has_recent_surgery')),

                                Toggle::make('is_eligible')
                                    ->label('مؤهل للتبرع')
                                    ->default(true)
                                    ->inline(false),
                            ])->columns(3),

                        Fieldset::make('معلومات التبرع')
                            ->schema([
                                DatePicker::make('last_donation_date')
                                    ->label('تاريخ آخر تبرع')
                                    ->native(false)
                                    ->maxDate(now()),

                                DatePicker::make('next_eligible_date')
                                    ->label('تاريخ الأهلية القادم')
                                    ->native(false)
                                    ->minDate(now()),

                                TextInput::make('total_donations')
                                    ->label('إجمالي عدد التبرعات')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->disabled()
                                    ->dehydrated(true),
                            ])->columns(3),
                    ]),
            ]);
    }
}
