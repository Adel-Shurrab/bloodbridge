<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Schemas\Schema;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\RichEditor;

class ManageGeneralSettings extends SettingsPage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'الإعدادات العامة';
    protected static ?string $title = 'الإعدادات العامة';
    protected static ?string $description = 'إدارة الإعدادات العامة';
    protected static string $settings = GeneralSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('إعدادات الموقع')
                    ->schema([
                        TextInput::make('site_name')
                            ->label('اسم الموقع')
                            ->required(),
                        Toggle::make('site_active')
                            ->label('تفعيل الوصول إلى الموقع')
                            ->helperText('قم بتعطيل هذا الخيار لوضع الموقع في وضع الصيانة.'),
                    ]),
                Section::make('المحتوى والاتصال')
                    ->schema([
                        RichEditor::make('main_content')
                            ->label('محتوى الصفحة الرئيسية')
                            ->columnSpanFull(),
                        TextInput::make('support_email')
                            ->label('البريد الالكتروني للدعم')
                            ->email()
                            ->required(),
                    ]),
            ]);
    }
}
