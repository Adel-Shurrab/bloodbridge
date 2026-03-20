<?php

namespace App\Filament\Admin\Resources\BloodRequests\Schemas;

use App\Models\BloodRequest;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use App\Models\Donor;
use Filament\Schemas\Schema;

use Filament\Forms\Components\Select;

class BloodRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('organization_id')
                    ->label(__('Organization'))
                    ->relationship('organization', 'org_name')
                    ->searchable()
                    ->preload(),
                Select::make('blood_type')
                    ->label(__('Blood Type'))
                    ->options(\App\Enums\BloodType::class),
                TextInput::make('units_needed')
                    ->label(__('Units Needed'))
                    ->numeric(),
                Select::make('urgency_level')
                    ->label(__('Urgency Level'))
                    ->options(\App\Enums\UrgencyLevel::class),
                Select::make('status')
                    ->label(__('Status'))
                    ->options(\App\Enums\BloodRequestStatus::class),
                Textarea::make('additional_notes')
                    ->label(__('Additional Notes'))
                    ->columnSpanFull(),
                DateTimePicker::make('broadcasted_at')
                    ->label(__('Broadcast Date'))
                    ->visibleOn('view'),
                DateTimePicker::make('fulfilled_at')
                    ->label(__('Fulfilled Date'))
                    ->visibleOn('view'),
            ]);
    }
}
