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

class ChangePassword extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-m-key';
    protected static ?string $navigationLabel = 'تغيير كلمة المرور';
    protected static ?int $navigationSort = 3;

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
                Section::make('أمان الحساب')
                    ->description('قم بتحديث كلمة المرور الخاصة بك بانتظام للحفاظ على أمان حسابك.')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('كلمة المرور الحالية')
                            ->password()
                            ->revealable()
                            ->required()
                            ->currentPassword(),

                        TextInput::make('new_password')
                            ->label('كلمة المرور الجديدة')
                            ->password()
                            ->revealable()
                            ->required()
                            ->rule(Password::defaults())
                            ->confirmed(),

                        TextInput::make('new_password_confirmation')
                            ->label('تأكيد كلمة المرور الجديدة')
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
            ->title('تم تغيير كلمة المرور بنجاح')
            ->send();
    }
}
