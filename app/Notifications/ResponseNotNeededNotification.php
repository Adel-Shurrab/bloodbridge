<?php

namespace App\Notifications;

use App\Models\RequestResponse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Actions\Action;

/**
 * Response Not Needed Notification
 * 
 * Sent to donor when their response is no longer needed (request fulfilled).
 * Delivery: Database + Real-time Broadcast (Reverb)
 * 
 * Triggered by: CancelExcessResponsesJob
 * Recipient: Donor (User)
 */
class ResponseNotNeededNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private RequestResponse $response;

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
        return ['database', 'broadcast'];
    }

    private function buildFilamentNotification(object $notifiable): FilamentNotification
    {
        return FilamentNotification::make()
            ->title(__('Thank you for your noble initiative 🤍'))
            ->body($this->getBody())
            ->icon('heroicon-o-heart')
            ->iconColor('danger')
            ->actions([
                Action::make('view')
                    ->label(__('View History'))
                    ->url(route('filament.donor.pages.history'))
                    ->button()
                    ->markAsRead(),
            ]);
    }

    private function getBody(): string
    {
        return __('The required blood units have been secured thanks to other donors. We apologize for canceling your appointment, and we hope you will join us in saving another life soon.');
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->buildFilamentNotification($notifiable)->getDatabaseMessage();
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return $this->buildFilamentNotification($notifiable)->getBroadcastMessage();
    }
}
