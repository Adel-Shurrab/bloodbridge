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
                    ->label(__('admin.name')),
                TextEntry::make('email')
                    ->label(__('admin.email')),
                TextEntry::make('subject')
                    ->label(__('admin.subject')),
                TextEntry::make('message')
                    ->label(__('admin.message'))
                    ->columnSpanFull(),
                TextEntry::make('ip_address')
                    ->label(__('admin.ip_address'))
                    ->placeholder('-'),
                TextEntry::make('read_at')
                    ->label(__('admin.read_at'))
                    ->dateTime()
                    ->placeholder(__('admin.not_read_yet')),
                TextEntry::make('created_at')
                    ->label(__('admin.sent_at'))
                    ->dateTime(),
            ]);
    }
}
