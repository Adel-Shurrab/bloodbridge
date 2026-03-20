<?php

namespace App\Filament\Admin\Resources\ContactMessages\Pages;

use App\Filament\Admin\Resources\ContactMessages\ContactMessageResource;
use Filament\Resources\Pages\ListRecords;
use LaraZeus\SpatieTranslatable\Resources\Pages\ListRecords\Concerns\Translatable;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use Filament\Actions;

use Filament\Schemas\Components\Tabs\Tab;

class ListContactMessages extends ListRecords
{
    use Translatable;

    protected static string $resource = ContactMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('All')),
            'unread' => Tab::make(__('Unread'))
                ->modifyQueryUsing(fn($query) => $query->whereNull('read_at')),
            'read' => Tab::make(__('Read'))
                ->modifyQueryUsing(fn($query) => $query->whereNotNull('read_at')),
        ];
    }
}
