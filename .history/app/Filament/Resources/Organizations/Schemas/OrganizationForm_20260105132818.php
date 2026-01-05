<?php

namespace App\Filament\Resources\Organizations\Schemas;

use App\Models\User;
use App\Models\Organization;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Radio;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Placeholder;

class OrganizationForm
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
                            ->visible(fn($get) => $get('user_creation_mode') === 'create')
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                if ($get('user_creation_mode') === 'create') {
                                    $set('contact_phone', $state);
                                }
                            }),

                        TextInput::make('new_user_password')
                            ->label('كلمة المرور')
                            ->password()
                            ->required(fn($get) => $get('user_creation_mode') === 'create')
                            ->minLength(8)
                            ->same('new_user_password_confirmation')
                            ->visible(fn($get) => $get('user_creation_mode') === 'create'),

                        TextInput::make('new_user_password_confirmation')
                            ->label('تأكيد كلمة المرور')
                            ->password()
                            ->required(fn($get) => $get('user_creation_mode') === 'create')
                            ->minLength(8)
                            ->visible(fn($get) => $get('user_creation_mode') === 'create'),

                        // Existing User Selection
                        Select::make('user_id')
                            ->label('الحساب المرتبط')
                            ->relationship('user', 'name', fn($query) => $query->where('role', User::ROLE_ORGANIZATION))
                            ->getOptionLabelFromRecordUsing(function (User $user, $record) {
                                $isOccupied = Organization::where('user_id', $user->id)
                                    ->when($record, fn($q) => $q->where('id', '!=', $record->id))
                                    ->exists();
                                return $isOccupied ? "{$user->name} (مشغول)" : $user->name;
                            })
                            ->disableOptionWhen(function (string $value, $record) {
                                return Organization::where('user_id', $value)
                                    ->when($record, fn($q) => $q->where('id', '!=', $record->id))
                                    ->exists();
                            })
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $operation) {
                                if ($operation !== 'create') return;

                                if (! $state) {
                                    $set('contact_phone', null);
                                    return;
                                }

                                $user = User::find($state);
                                if ($user) {
                                    $set('contact_phone', $user->phone);
                                }
                            })
                            ->required(fn($get) => $get('user_creation_mode') === 'select')
                            ->searchable()
                            ->preload()
                            ->visible(fn($get) => $get('user_creation_mode') === 'select'),
                    ])->columns(2),

                Section::make('معلومات المنظمة')
                    ->description('المعلومات الأساسية والترخيص')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        TextInput::make('org_name')
                            ->label('اسم المنظمة')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('license_number')
                            ->label('رقم الترخيص')
                            ->required()
                            ->maxLength(255),

                        FileUpload::make('license_document_path')
                            ->label('وثيقة الترخيص')
                            ->image()
                            ->disk('public')
                            ->directory('organization-licenses')
                            ->visibility('public')
                            ->openable()
                            ->downloadable(),

                        TextInput::make('description')
                            ->label('وصف المنظمة')
                            ->columnSpanFull(),

                        TextInput::make('user.name')
                            ->label('اسم المسؤول')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('responsible_person_position')
                            ->label('منصب المسؤول')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('user.email')
                            ->label('البريد الإلكتروني للمسؤول')
                            ->email()
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),


                Section::make('معلومات التواصل العام')
                    ->description('كيف يمكن للجمهور التواصل مع المنظمة')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        TextInput::make('contact_email')
                            ->label('البريد الإلكتروني للتواصل')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        TextInput::make('contact_phone')
                            ->label('رقم هاتف التواصل')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),

                Section::make('الموقع وساعات العمل')
                    ->description('تفاصيل الموقع الجغرافي وأوقات الدوام')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('governorate_id')
                                    ->label('المحافظة')
                                    ->relationship('governorate', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                TextInput::make('street_address')
                                    ->label('العنوان بالتفصيل')
                                    ->required()
                                    ->maxLength(255),

                                TimePicker::make('opening_time')
                                    ->label('وقت الفتح'),

                                TimePicker::make('closing_time')
                                    ->label('وقت الإغلاق'),

                                Placeholder::make('24_7_indicator')
                                    ->label('ساعات العمل')
                                    ->content('يعمل على مدار 24 ساعة')
                                    ->visible(fn($record) => $record && $record->opening_time === null && $record->closing_time === null),
                            ]),

                        CheckboxList::make('working_days')
                            ->label('أيام العمل')
                            ->options([
                                'Saturday' => 'السبت',
                                'Sunday' => 'الأحد',
                                'Monday' => 'الاثنين',
                                'Tuesday' => 'الثلاثاء',
                                'Wednesday' => 'الأربعاء',
                                'Thursday' => 'الخميس',
                                'Friday' => 'الجمعة',
                            ])
                            ->columns(4)
                            ->required()
                            ->columnSpanFull()
                            ->hidden(fn($operation) => $operation === 'view'),

                        Placeholder::make('working_days_display')
                            ->label('أيام العمل')
                            ->content(function ($record) {
                                if (! $record || ! $record->working_days) {
                                    return '-';
                                }

                                $days = [
                                    'Saturday' => 'السبت',
                                    'Sunday' => 'الأحد',
                                    'Monday' => 'الاثنين',
                                    'Tuesday' => 'الثلاثاء',
                                    'Wednesday' => 'الأربعاء',
                                    'Thursday' => 'الخميس',
                                    'Friday' => 'الجمعة',
                                ];

                                return collect($record->working_days)
                                    ->map(fn($day) => $days[$day] ?? $day)
                                    ->implode('، ');
                            })
                            ->columnSpanFull()
                            ->visible(fn($operation) => $operation === 'view'),

                        TextInput::make('daily_capacity')
                            ->label('القدرة اليومية')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                    ])->columns(2),
            ]);
    }
}
