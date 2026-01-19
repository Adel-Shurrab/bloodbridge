<?php

namespace App\Filament\Organization\Pages;

use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Pages\Tenancy\EditTenantProfile;

class EditOrganizationProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'ملف المنظمة';
    }

    protected static ?string $title = 'إدارة ملف المنظمة';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                // المعلومات الأساسية
                Section::make('المعلومات الأساسية')
                    ->description('البيانات الأساسية للمستشفى أو المركز الطبي')
                    ->icon('heroicon-o-building-office-2')
                    ->iconColor('primary')
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('org_name')
                                    ->label('اسم المنظمة / المركز الطبي')
                                    ->placeholder('أدخل اسم المنظمة')
                                    ->required()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-m-building-office')
                                    ->helperText('الاسم الرسمي للمنظمة أو المركز الطبي')
                                    ->live(onBlur: true)
                                    ->columnSpan(2),

                                Textarea::make('description')
                                    ->label('وصف المنظمة')
                                    ->placeholder('أدخل وصفاً مختصراً عن المنظمة وخدماتها...')
                                    ->rows(4)
                                    ->maxLength(1000)
                                    ->helperText('نبذة تعريفية عن المنظمة والخدمات التي تقدمها (حتى 1000 حرف)')
                                    ->columnSpan(2),
                            ]),
                    ]),

                // معلومات التواصل
                Section::make('معلومات التواصل')
                    ->description('بيانات الاتصال والموقع الجغرافي للمنظمة')
                    ->icon('heroicon-o-phone')
                    ->iconColor('success')
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('contact_email')
                                    ->label('البريد الإلكتروني')
                                    ->placeholder('example@hospital.ps')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-m-envelope')
                                    ->helperText('البريد الإلكتروني الرسمي للتواصل')
                                    ->suffixIcon('heroicon-m-at-symbol')
                                    ->columnSpan(1),

                                TextInput::make('contact_phone')
                                    ->label('رقم الهاتف')
                                    ->placeholder('970591234567')
                                    ->tel()
                                    ->required()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-m-phone')
                                    ->helperText('رقم الهاتف الأرضي أو المحمول')
                                    ->columnSpan(1),

                                TextInput::make('street_address')
                                    ->label('العنوان التفصيلي')
                                    ->placeholder('المدينة، الحي، الشارع، رقم المبنى')
                                    ->required()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-m-map-pin')
                                    ->helperText('العنوان الكامل لتسهيل الوصول إلى المركز')
                                    ->columnSpan(2),
                            ]),
                    ]),

                // أوقات العمل والطاقة الاستيعابية
                Section::make('أوقات العمل والطاقة الاستيعابية')
                    ->description('ساعات العمل اليومية وقدرة المركز على استقبال المتبرعين')
                    ->icon('heroicon-o-clock')
                    ->iconColor('warning')
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        // Working Hours
                        Group::make()
                            ->schema([
                                Placeholder::make('working_hours_label')
                                    ->label('ساعات العمل')
                                    ->content('حدد أوقات الافتتاح والإغلاق اليومية'),

                                Toggle::make('emergency_available')
                                    ->label('مناوبة الحالات الطارئة 24/7')
                                    ->helperText('عند التفعيل، المركز يعمل على مدار الساعة')
                                    ->default(false)
                                    ->inline(false)
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            // Clear opening and closing times when emergency mode is enabled
                                            $set('opening_time', null);
                                            $set('closing_time', null);
                                        }
                                    }),

                                Grid::make(3)
                                    ->schema([
                                        TimePicker::make('opening_time')
                                            ->label('وقت الافتتاح')
                                            ->placeholder('08:00')
                                            ->seconds(false)
                                            ->native(false)
                                            ->prefixIcon('heroicon-m-clock')
                                            ->helperText('وقت بدء استقبال المتبرعين')
                                            ->displayFormat('H:i')
                                            ->visible(fn (callable $get) => !$get('emergency_available'))
                                            ->required(fn (callable $get) => !$get('emergency_available'))
                                            ->columnSpan(1),

                                        TimePicker::make('closing_time')
                                            ->label('وقت الإغلاق')
                                            ->placeholder('16:00')
                                            ->seconds(false)
                                            ->native(false)
                                            ->prefixIcon('heroicon-m-clock')
                                            ->helperText('وقت انتهاء استقبال المتبرعين')
                                            ->displayFormat('H:i')
                                            ->after('opening_time')
                                            ->visible(fn (callable $get) => !$get('emergency_available'))
                                            ->required(fn (callable $get) => !$get('emergency_available'))
                                            ->columnSpan(1),

                                        TextInput::make('daily_capacity')
                                            ->label('الطاقة الاستيعابية اليومية')
                                            ->placeholder('50')
                                            ->numeric()
                                            ->required()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(1000)
                                            ->suffix('متبرع/يوم')
                                            ->prefixIcon('heroicon-m-user-group')
                                            ->helperText('عدد المتبرعين الذين يمكن استقبالهم يومياً')
                                            ->columnSpan(fn (callable $get) => $get('emergency_available') ? 3 : 1),
                                    ]),
                            ]),

                        // Working Days
                        Group::make()
                            ->schema([
                                Placeholder::make('working_days_label')
                                    ->label('أيام العمل الأسبوعية')
                                    ->content('اختر أيام العمل التي يستقبل فيها المركز المتبرعين'),

                                CheckboxList::make('working_days')
                                    ->label('')
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
                                    ->gridDirection('row')
                                    ->bulkToggleable()
                                    ->helperText('يمكنك تحديد أو إلغاء جميع الأيام دفعة واحدة')
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}