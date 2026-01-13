<?php

namespace App\Filament\Resources\BloodRequests\Schemas;

use App\Models\BloodRequest;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Schemas\Schema;

class BloodRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('organization_id')
                    ->label('المنظمة')
                    ->disabled(),
                TextInput::make('blood_type')
                    ->label('فصيلة الدم')
                    ->formatStateUsing(fn($state) => \App\Models\Donor::getBloodTypeOptions()[$state] ?? $state)
                    ->disabled(),
                TextInput::make('units_needed')
                    ->label('الوحدات المطلوبة')
                    ->numeric(),
                TextInput::make('urgency_level')
                    ->label('مستوى الاستعجال')
                    ->formatStateUsing(fn($state) => BloodRequest::getUrgencyOptions()[$state] ?? $state)
                    ->disabled(),
                TextInput::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn($state) => BloodRequest::getStatusOptions()[$state] ?? $state)
                    ->disabled(),
                Textarea::make('additional_notes')
                    ->label('ملاحظات إضافية')
                    ->columnSpanFull(),
                DateTimePicker::make('broadcasted_at')
                    ->label('تاريخ البث')
                    ->disabled(),
                DateTimePicker::make('fulfilled_at')
                    ->label('تاريخ الاكتمال')
                    ->disabled(),
            ]);
    }
}
