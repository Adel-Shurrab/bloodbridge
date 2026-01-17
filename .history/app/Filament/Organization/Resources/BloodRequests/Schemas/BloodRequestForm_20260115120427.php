<?php

namespace App\Filament\Organization\Resources\BloodRequests\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class BloodRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('organization_id')
                    ->required()
                    ->numeric(),
                TextInput::make('blood_type')
                    ->required()
                    ->numeric(),
                TextInput::make('units_needed')
                    ->required()
                    ->numeric(),
                TextInput::make('urgency_level')
                    ->required()
                    ->numeric()
                    ->default(1),
                Textarea::make('additional_notes')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('search_radius_km')
                    ->required()
                    ->numeric()
                    ->default(10),
                TextInput::make('status')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('donors_accepted')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('donors_completed')
                    ->required()
                    ->numeric()
                    ->default(0),
                DateTimePicker::make('broadcasted_at'),
                DateTimePicker::make('fulfilled_at'),
            ]);
    }
}
