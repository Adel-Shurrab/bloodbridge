<?php

namespace App\Filament\Resources\Organizations\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;

class OrganizationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                    ])->columns(2),

                Section::make('الشخص المسؤول')
                    ->description('بيانات التواصل مع الشخص المسؤول')
                    ->icon('heroicon-o-user')
                    ->schema([
                        TextInput::make('responsible_person_name')
                            ->label('اسم المسؤول')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('responsible_person_position')
                            ->label('المنصب')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('responsible_person_email')
                            ->label('البريد الإلكتروني للمسؤول')
                            ->email()
                            ->required()
                            ->maxLength(255),
                    ])->columns(3),

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
                        \Filament\Forms\Components\Select::make('governorate_id')
                            ->label('المحافظة')
                            ->relationship('governorate', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        TextInput::make('street_address')
                            ->label('العنوان بالتفصيل')
                            ->required()
                            ->maxLength(255),

                        \Filament\Forms\Components\TimePicker::make('opening_time')
                            ->label('وقت الفتح'),

                        \Filament\Forms\Components\TimePicker::make('closing_time')
                            ->label('وقت الإغلاق'),

                        \Filament\Forms\Components\CheckboxList::make('working_days')
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
                            ->columns(2)
                            ->gridDirection('row')
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('daily_capacity')
                            ->label('القدرة اليومية')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                    ])->columns(2),
            ]);
    }
}
