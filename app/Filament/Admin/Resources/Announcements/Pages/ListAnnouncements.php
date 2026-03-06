<?php

namespace App\Filament\Admin\Resources\Announcements\Pages;

use App\Filament\Admin\Resources\Announcements\AnnouncementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListAnnouncements extends ListRecords
{
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
            'all' => Tab::make('الكل'),
            'sent' => Tab::make('المرسلة')
                ->modifyQueryUsing(fn($query) => $query->where('status', 1)),
            'draft' => Tab::make('المسودات')
                ->modifyQueryUsing(fn($query) => $query->where('status', 0)),
        ];
    }
}
