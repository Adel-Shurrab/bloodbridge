<?php

namespace App\Filament\Pages;

use App\Settings\DonationSettings;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;

use BackedEnum;

class ManageDonationSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'إعدادات التبرع';
    protected static ?string $title = 'إعدادات قواعد التبرع';
    protected static string $settings = DonationSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('المتطلبات الجسدية')
                    ->schema([
                        TextInput::make('min_weight')
                            ->label('أقل وزن مقبول (كجم)')
                            ->numeric()
                            ->required(),
                        TextInput::make('min_height')
                            ->label('أقل طول مقبول (سم)')
                            ->numeric()
                            ->required(),
                    ])->columns(2),

                Section::make('فترات الانتظار (بالأيام)')
                    ->schema([
                        TextInput::make('donation_deferral_days')
                            ->label('فترة الانتظار بعد التبرع')
                            ->numeric()
                            ->required(),
                        TextInput::make('surgery_deferral_days')
                            ->label('فترة الانتظار بعد العمليات الجراحية')
                            ->numeric()
                            ->required(),
                        TextInput::make('infection_deferral_days')
                            ->label('فترة الانتظار بعد الإصابات/العدوى')
                            ->numeric()
                            ->required(),
                    ])->columns(3),
            ]);
    }
}
