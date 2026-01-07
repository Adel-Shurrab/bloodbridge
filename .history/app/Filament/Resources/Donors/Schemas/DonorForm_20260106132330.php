<?php

namespace App\Filament\Resources\Donors\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;

class DonorForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('اسم المستخدم')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('national_id')
                    ->label('رقم الهوية')
                    ->required()
                    ->maxLength(9),
                Select::make('gender')
                    ->label('الجنس')
                    ->options(\App\Models\Donor::getGenderOptions())
                    ->required(),
                DatePicker::make('birth_date')
                    ->label('تاريخ الميلاد'),
                Select::make('governorate_id')
                    ->relationship('governorate', 'name')
                    ->label('المحافظة')
                    ->searchable()
                    ->preload(),

                // Health Profile Section (HasOne)
                Fieldset::make('الملف الصحي')
                    ->relationship('healthProfile')
                    ->schema([
                        Select::make('blood_type')
                            ->label('فصيلة الدم')
                            ->options(\App\Models\Donor::getBloodTypeOptions())
                            ->required(),
                    ]),
            ]);
    }
}
