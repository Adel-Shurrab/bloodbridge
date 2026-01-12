<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageGeneralSettings extends SettingsPage
{
    protected string $view = 'filament.pages.manage-general-settings';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Website Settings';
    protected static ?string $title = 'General Settings';

    protected static string $settings = GeneralSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Site Configuration')
                    ->schema([
                        Forms\Components\TextInput::make('site_name')
                            ->label('Website Name')
                            ->required(),
                        Forms\Components\Toggle::make('site_active')
                            ->label('Enable Website Access')
                            ->helperText('Turn this off to put the site in maintenance mode.'),
                    ]),
                Forms\Components\Section::make('Content & Contact')
                    ->schema([
                        Forms\Components\RichEditor::make('main_content')
                            ->label('Main Page Content')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('support_email')
                            ->label('Support Email')
                            ->email()
                            ->required(),
                    ]),
            ]);
    }
}
