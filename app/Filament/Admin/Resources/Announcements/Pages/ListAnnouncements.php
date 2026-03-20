<?php

namespace App\Filament\Admin\Resources\Announcements\Pages;

use App\Filament\Admin\Resources\Announcements\AnnouncementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use LaraZeus\SpatieTranslatable\Resources\Pages\ListRecords\Concerns\Translatable;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;

use Filament\Schemas\Components\Tabs\Tab;

class ListAnnouncements extends ListRecords
{
    use Translatable;

    protected static string $resource = AnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('All')),
            'sent' => Tab::make(__('Sent'))
                ->modifyQueryUsing(fn($query) => $query->where('status', 1)),
            'draft' => Tab::make(__('Drafts'))
                ->modifyQueryUsing(fn($query) => $query->where('status', 0)),
        ];
    }
}
