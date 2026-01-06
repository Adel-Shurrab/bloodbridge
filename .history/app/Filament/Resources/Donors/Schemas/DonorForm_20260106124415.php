<?php

namespace App\Filament\Resources\Donors\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DonorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('national_id')
                    ->required()
                    ->maxLength(9),
                Select::make('gender')
                    ->options(\App\Models\Donor::GENDER_LIST)
                    ->required(),
                DatePicker::make('birth_date'),
                Select::make('governorate_id')
                    ->relationship('governorate', 'name')
                    ->searchable()
                    ->preload(),

                // Health Profile Section (HasOne)
                \Filament\Forms\Components\Fieldset::make('Health Profile')
                    ->relationship('healthProfile')
                    ->schema([
                        Select::make('blood_type')
                            ->options(\App\Models\Donor::getBloodTypeOptions())
                            ->required(),
                    ]),
            ]);
    }
}
