<?php

namespace App\Filament\Admin\Resources\ContactMessages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ContactMessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('الاسم'),
                TextEntry::make('email')
                    ->label('البريد الإلكتروني'),
                TextEntry::make('subject')
                    ->label('الموضوع'),
                TextEntry::make('message')
                    ->label('الرسالة')
                    ->columnSpanFull(),
                TextEntry::make('ip_address')
                    ->label('عنوان IP')
                    ->placeholder('-'),
                TextEntry::make('read_at')
                    ->label('تاريخ القراءة')
                    ->dateTime()
                    ->placeholder('غير مقروءة بعد'),
                TextEntry::make('created_at')
                    ->label('تاريخ الإرسال')
                    ->dateTime(),
            ]);
    }
}
