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
                    ->label(__('Name')),
                TextEntry::make('email')
                    ->label(__('Email')),
                TextEntry::make('subject')
                    ->label(__('Subject')),
                TextEntry::make('message')
                    ->label(__('Message'))
                    ->columnSpanFull(),
                TextEntry::make('ip_address')
                    ->label(__('IP Address'))
                    ->placeholder('-'),
                TextEntry::make('read_at')
                    ->label(__('Read At'))
                    ->dateTime()
                    ->placeholder(__('Not read yet')),
                TextEntry::make('created_at')
                    ->label(__('Sent At'))
                    ->dateTime(),
            ]);
    }
}
