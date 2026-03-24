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
                    ->label(__('admin.name'))
                    ->disabled(),
                TextInput::make('email')
                    ->label(__('admin.email'))
                    ->email()
                    ->disabled(),
                TextInput::make('subject')
                    ->label(__('admin.subject'))
                    ->disabled(),
                Textarea::make('message')
                    ->label(__('admin.message'))
                    ->disabled()
                    ->columnSpanFull(),
                TextInput::make('ip_address')
                    ->label(__('admin.ip_address'))
                    ->disabled(),
                DateTimePicker::make('read_at')
                    ->label(__('admin.read_at')),
            ]);
    }
}
