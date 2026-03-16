<?php

namespace App\Notifications;

use App\Models\RequestResponse;
use App\Enums\RequestResponseStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Actions\Action;

/**
 * Donor Response Notification
 * 
 * Sent to organization when donor responds to a blood request.
 * Delivery: Database + Real-time Broadcast (Reverb)
 * 
 * Triggered by: BloodRequestActionService::accept()
 * Recipient: Organization Admin (User)
 */
class DonorResponseNotification extends Notification implements ShouldQueue
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
            ->title($this->getTitle())
            ->body($this->getBody())
            ->icon($this->getIcon())
            ->iconColor($this->getIconColor())
            ->actions([
                Action::make('view')
                    ->label(__('View Response'))
                    ->url($this->getResponseUrl())
                    ->button()
                    ->markAsRead(),
            ]);
    }

    private function getTitle(): string
    {
        return match ($this->response->status) {
            RequestResponseStatus::PENDING => __('New donor accepted donation request'),
            RequestResponseStatus::ACCEPTED => __('Donor arrived at hospital'),
            RequestResponseStatus::COMPLETED => __('Donation completed'),
            RequestResponseStatus::DECLINED => __('Donation medically declined'),
            RequestResponseStatus::NO_SHOW => __('Donor did not show up'),
            default => __('Donor response'),
        };
    }

    private function getBody(): string
    {
        $donor = $this->response->donor;
        $bloodType = $donor->healthProfile?->blood_type;
        
        $body = $donor->user->name . " - " . __('Blood Type') . ": ";
        $body .= $bloodType?->getLabel() ?? __('Not specified');

        if ($this->response->distance !== null) {
            $distanceKm = round($this->response->distance, 1);
            $body .= " - " . __('Distance: :distance km', ['distance' => $distanceKm]);
        }

        return $body;
    }

    private function getIcon(): string
    {
        return match ($this->response->status) {
            RequestResponseStatus::COMPLETED => 'heroicon-o-check-circle',
            RequestResponseStatus::DECLINED, RequestResponseStatus::NO_SHOW => 'heroicon-o-x-circle',
            default => 'heroicon-o-user',
        };
    }

    private function getIconColor(): string
    {
        return match ($this->response->status) {
            RequestResponseStatus::COMPLETED => 'success',
            RequestResponseStatus::DECLINED, RequestResponseStatus::NO_SHOW => 'danger',
            RequestResponseStatus::ACCEPTED => 'info',
            default => 'warning',
        };
    }

    private function getResponseUrl(): string
    {
        $request = $this->response->bloodRequest;
        return route('filament.organization.resources.blood-requests.view', [
            'tenant' => $request->organization->slug,
            'record' => $request->id,
        ]);
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
