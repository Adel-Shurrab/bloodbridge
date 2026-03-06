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
                    ->label('الاسم')
                    ->disabled(),
                TextInput::make('email')
                    ->label('البريد الإلكتروني')
                    ->email()
                    ->disabled(),
                TextInput::make('subject')
                    ->label('الموضوع')
                    ->disabled(),
                Textarea::make('message')
                    ->label('الرسالة')
                    ->disabled()
                    ->columnSpanFull(),
                TextInput::make('ip_address')
                    ->label('عنوان IP')
                    ->disabled(),
                DateTimePicker::make('read_at')
                    ->label('تاريخ القراءة'),
            ]);
    }
}
