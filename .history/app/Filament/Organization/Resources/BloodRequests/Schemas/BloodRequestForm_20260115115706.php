<?php

namespace App\Filament\Organization\Resources\BloodRequests\Schemas;

use App\Models\BloodRequest;
use App\Models\Donor;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BloodRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('organization_id'),

                Select::make('blood_type')
                    ->label('فصيلة الدم المطلوبة')
                    ->options(Donor::getBloodTypeOptions())
                    ->required()
                    ->native(false),

                TextInput::make('units_needed')
                    ->label('عدد الوحدات')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->default(1),

                Select::make('urgency_level')
                    ->label('مستوى الاستعجال')
                    ->options(BloodRequest::getUrgencyOptions())
                    ->required()
                    ->default(BloodRequest::URGENCY_LOW)
                    ->native(false),

                TextInput::make('search_radius_km')
                    ->label('نطاق البحث (كم)')
                    ->helperText('المسافة التي سيتم إرسال الإشعارات للمتبرعين خلالها')
                    ->required()
                    ->numeric()
                    ->default(10),

                Textarea::make('additional_notes')
                    ->label('ملاحظات إضافية')
                    ->columnSpanFull(),

                Hidden::make('status')
                    ->default(BloodRequest::STATUS_PENDING),

                Hidden::make('donors_accepted')->default(0),
                Hidden::make('donors_completed')->default(0),
            ]);
    }
}
