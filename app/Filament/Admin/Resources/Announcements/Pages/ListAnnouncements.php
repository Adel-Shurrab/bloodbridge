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
            'all' => Tab::make(__('admin.all')),
            'sent' => Tab::make(__('admin.sent'))
                ->modifyQueryUsing(fn($query) => $query->where('status', 1)),
            'draft' => Tab::make(__('admin.drafts'))
                ->modifyQueryUsing(fn($query) => $query->where('status', 0)),
        ];
    }
}
