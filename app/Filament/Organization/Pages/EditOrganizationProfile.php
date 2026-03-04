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
use Dotswan\MapPicker\Fields\Map;
use App\Constants\PalestineCoordinates;

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

                                Map::make('location')
                                    ->label('موقع المنظمة على الخريطة')
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

                Section::make('أوقات العمل والطاقة الاستيعابية')
                    ->description('ساعات العمل اليومية وقدرة المركز على استقبال المتبرعين')
                    ->icon('heroicon-o-clock')
                    ->iconColor('warning')
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        
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
                                            ->label('وقت الافتتاح')
                                            ->placeholder('08:00')
                                            ->seconds(false)
                                            ->native(false)
                                            ->prefixIcon('heroicon-m-clock')
                                            ->helperText('وقت بدء استقبال المتبرعين')
                                            ->displayFormat('H:i')
                                            ->visible(fn(callable $get) => !$get('emergency_available'))
                                            ->required(fn(callable $get) => !$get('emergency_available'))
                                            ->dehydrated()
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
                                            ->visible(fn(callable $get) => !$get('emergency_available'))
                                            ->required(fn(callable $get) => !$get('emergency_available'))
                                            ->dehydrated()
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
                                            ->columnSpan(fn(callable $get) => $get('emergency_available') ? 3 : 1),
                                    ]),
                            ]),

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

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        if ($data['emergency_available'] ?? false) {
            $data['opening_time'] = null;
            $data['closing_time'] = null;
        }

        $record->update($data);

        return $record;
    }
}
