<?php

namespace App\Filament\Resources\BloodRequests\Schemas;

use App\Models\BloodRequest;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Schemas\Schema;

use Filament\Forms\Components\Select;

class BloodRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('organization_id')
                    ->label('المنظمة')
                    ->relationship('organization', 'org_name')
                    ->searchable()
                    ->preload(),
                Select::make('blood_type')
                    ->label('فصيلة الدم')
                    ->options(\App\Models\Donor::getBloodTypeOptions()),
                TextInput::make('units_needed')
                    ->label('الوحدات المطلوبة')
                    ->numeric(),
                Select::make('urgency_level')
                    ->label('مستوى الاستعجال')
                    ->options(BloodRequest::getUrgencyOptions()),
                Select::make('status')
                    ->label('الحالة')
                    ->options(BloodRequest::getStatusOptions()),
                Textarea::make('additional_notes')
                    ->label('ملاحظات إضافية')
                    ->columnSpanFull(),
                
            ]);
    }
}
