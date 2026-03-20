<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use App\Models\User;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('User Information'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('Name'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label(__('Email'))
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique('users', 'email', ignoreRecord: true),

                        TextInput::make('phone')
                            ->label(__('Phone Number'))
                            ->tel()
                            ->required()
                            ->maxLength(255)
                            ->unique('users', 'phone', ignoreRecord: true),

                        Select::make('role')
                            ->label(__('Account Type'))
                            ->options(\App\Enums\UserRole::class)
                            ->required(),

                        Toggle::make('is_active')
                            ->label(__('Active'))
                            ->default(true)
                            ->required(),

                        TextInput::make('password')
                            ->label(__('Password'))
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
