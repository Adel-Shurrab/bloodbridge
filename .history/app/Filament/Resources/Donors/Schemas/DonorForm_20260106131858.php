<?php

namespace App\Filament\Resources\Donors\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Fieldset;

class DonorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
