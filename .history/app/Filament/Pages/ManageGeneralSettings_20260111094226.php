<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class ManageGeneralSettings extends SettingsPage
{
    protected string $view = 'filament.pages.manage-general-settings';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Website Settings';
    protected static ?string $title = 'General Settings';

    protected static string $settings = GeneralSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Site Configuration')
                    ->schema([
                        TextInput::make('site_name')
                            ->label('Website Name')
                            ->required(),
                        Toggle::make('site_active')
                            ->label('Enable Website Access')
                            ->helperText('Turn this off to put the site in maintenance mode.'),
                    ]),
                Section::make('Content & Contact')
                    ->schema([
                        Schemas\Components\RichEditor::make('main_content')
                            ->label('Main Page Content')
                            ->columnSpanFull(),
                        Schemas\Components\TextInput::make('support_email')
                            ->label('Support Email')
                            ->email()
                            ->required(),
                    ]),
            ]);
    }
}
