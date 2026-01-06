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
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('governorate_id')
                    ->numeric()
                    ->default(null),
                TextInput::make('national_id')
                    ->required(),
                Select::make('gender')
                    ->options(['male' => 'Male', 'female' => 'Female'])
                    ->required(),
                DatePicker::make('birth_date'),
                TextInput::make('lat')
                    ->numeric()
                    ->default(null),
                TextInput::make('lng')
                    ->numeric()
                    ->default(null),
                TextInput::make('points')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('level')
                    ->required()
                    ->numeric()
                    ->default(1),
            ]);
    }
}
