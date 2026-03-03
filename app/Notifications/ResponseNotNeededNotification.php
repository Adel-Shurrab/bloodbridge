<?php

namespace App\Notifications;

use App\Models\RequestResponse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Actions\Action;

class ResponseNotNeededNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected RequestResponse $response;

    /**
     * Create a new notification instance.
     */
    public function __construct(RequestResponse $response)
    {
        $this->response = $response;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the database notification representation.
     */
    public function toDatabase(object $notifiable): array
    {
        $bloodRequest = $this->response->bloodRequest;

        return FilamentNotification::make()
            ->title('شكراً لمبادرتك النبيلة 🤍')
            ->body('لقد تم تأمين وحدات الدم المطلوبة للمريض بفضل متبرعين آخرين. نعتذر عن إلغاء موعدك، ونأمل أن تشاركنا في إنقاذ حياة أخرى قريباً.')
            ->icon('heroicon-o-heart')
            ->iconColor('danger')
            ->actions([
                Action::make('view')
                    ->label('عرض السجل')
                    ->url(route('filament.donor.pages.history'))
                    ->button()
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
