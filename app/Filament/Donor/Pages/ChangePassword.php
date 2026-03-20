<?php

namespace App\Filament\Donor\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use LaraZeus\SpatieTranslatable\Resources\Concerns\HasActiveLocaleSwitcher;

class ChangePassword extends Page implements HasForms
{
    use InteractsWithForms, HasActiveLocaleSwitcher {
        HasActiveLocaleSwitcher::getActiveFormsLocale insteadof InteractsWithForms;
        HasActiveLocaleSwitcher::getActiveActionsLocale insteadof InteractsWithForms;
        HasActiveLocaleSwitcher::getFilamentTranslatableContentDriver insteadof InteractsWithForms;
    }

    public ?string $activeLocale = null;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-m-key';
    public static function getNavigationLabel(): string
    {
        return __('Change Password');
    }
    public function getTitle(): string
    {
        return __('Change Password');
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.donor.pages.change-password';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make(__('Account Security'))
                    ->description(__('Update your password regularly to keep your account secure.'))
                    ->schema([
                        TextInput::make('current_password')
                            ->label(__('Current Password'))
                            ->password()
                            ->revealable()
                            ->required()
                            ->currentPassword(),

                        TextInput::make('new_password')
                            ->label(__('New Password'))
                            ->password()
                            ->revealable()
                            ->required()
                            ->rule(Password::defaults())
                            ->confirmed(),

                        TextInput::make('new_password_confirmation')
                            ->label(__('Confirm New Password'))
                            ->password()
                            ->revealable()
                            ->required()
                            ->dehydrated(false),
                    ])
                    ->columns(1),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Auth::user()->update([
            'password' => Hash::make($data['new_password']),
        ]);

        $this->form->fill();

        Notification::make()
            ->success()
            ->title(__('Password changed successfully'))
            ->send();
    }
}

