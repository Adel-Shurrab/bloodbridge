<?php

namespace App\Filament\Admin\Resources\ContactMessages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ContactMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('Name'))
                    ->disabled(),
                TextInput::make('email')
                    ->label(__('Email'))
                    ->email()
                    ->disabled(),
                TextInput::make('subject')
                    ->label(__('Subject'))
                    ->disabled(),
                Textarea::make('message')
                    ->label(__('Message'))
                    ->disabled()
                    ->columnSpanFull(),
                TextInput::make('ip_address')
                    ->label(__('IP Address'))
                    ->disabled(),
                DateTimePicker::make('read_at')
                    ->label(__('Read At')),
            ]);
    }
}
