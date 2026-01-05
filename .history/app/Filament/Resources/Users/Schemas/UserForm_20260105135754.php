<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Section::make('معلومات المستخدم')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('name')
                            ->label('الاسم')
                            ->required()
                            ->maxLength(255),

                        \Filament\Forms\Components\TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique('users', 'email', ignoreRecord: true),

                        \Filament\Forms\Components\TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->required()
                            ->maxLength(255)
                            ->unique('users', 'phone', ignoreRecord: true),

                        \Filament\Forms\Components\Select::make('role')
                            ->label('الصلاحية')
                            ->options(\App\Models\User::getRoleOptions())
                            ->required(),

                        \Filament\Forms\Components\Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true)
                            ->required(),

                        \Filament\Forms\Components\TextInput::make('password')
                            ->label('كلمة المرور')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn($operation) => $operation === 'create')
                            ->minLength(8)
                            ->maxLength(255),
                    ])->columns(2),
            ]);
    }
}
