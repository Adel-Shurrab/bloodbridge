<?php

namespace App\Filament\Admin\Resources\Announcements\Pages;

use App\Filament\Admin\Resources\Announcements\AnnouncementResource;
use Filament\Actions\EditAction;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use Filament\Resources\Pages\ViewRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\ViewRecord\Concerns\Translatable;
use Filament\Actions\Action;

class ViewAnnouncement extends ViewRecord
{
    use Translatable;

    protected static string $resource = AnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('publish')
                ->label(__('Send Now'))
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading(__('Confirm Send Announcement'))
                ->modalDescription(__('Confirm Send Announcement Description'))
                ->modalSubmitActionLabel(__('Yes, Send'))
                ->action(fn() => $this->record->update(['status' => 1]))
                ->visible(fn() => $this->record->status === 0),
            EditAction::make()
                ->visible(fn() => $this->record->status === 0),
        ];
    }
}
