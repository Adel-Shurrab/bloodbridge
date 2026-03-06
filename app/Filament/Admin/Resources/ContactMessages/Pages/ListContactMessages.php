<?php

namespace App\Filament\Admin\Resources\ContactMessages\Pages;

use App\Filament\Admin\Resources\ContactMessages\ContactMessageResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListContactMessages extends ListRecords
{
    protected static string $resource = ContactMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('الكل'),
            'unread' => Tab::make('غير مقروءة')
                ->modifyQueryUsing(fn($query) => $query->whereNull('read_at')),
            'read' => Tab::make('مقروءة')
                ->modifyQueryUsing(fn($query) => $query->whereNotNull('read_at')),
        ];
    }
}
