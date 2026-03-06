<?php

namespace App\Filament\Admin\Resources\Announcements\Pages;

use App\Filament\Admin\Resources\Announcements\AnnouncementResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;

class ViewAnnouncement extends ViewRecord
{
    protected static string $resource = AnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('publish')
                ->label('إرسال الآن')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('تأكيد إرسال الإعلان')
                ->modalDescription('هل أنت متأكد من رغبتك في إرسال هذا الإعلان الآن؟')
                ->modalSubmitActionLabel('نعم، أرسل')
                ->action(fn() => $this->record->update(['status' => 1]))
                ->visible(fn() => $this->record->status === 0),
            EditAction::make(),
        ];
    }
}
