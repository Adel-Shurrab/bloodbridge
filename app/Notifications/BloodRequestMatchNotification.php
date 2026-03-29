<?php

namespace App\Notifications;

use App\Models\BloodRequest;
use App\Enums\BloodType;
use App\Enums\UrgencyLevel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Actions\Action;

/**
 * Blood Request Match Notification
 * 
 * Sent to eligible donors when their blood type matches a request.
 * Delivery: Database + Real-time Broadcast (Reverb)
 * 
 * Triggered by: DispatchBloodRequestNotifications job
 * Recipient: Donor (User)
 */
class BloodRequestMatchNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private BloodRequest $bloodRequest;
    private ?float $distance;

    /**
     * Create a new notification instance.
     */
    public function __construct(BloodRequest $bloodRequest, ?float $distance = null)
    {
        $this->bloodRequest = $bloodRequest;
        $this->distance = $distance;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    private function buildFilamentNotification(object $notifiable): FilamentNotification
    {
        return FilamentNotification::make()
            ->title($this->getTitle())
            ->body($this->getBody($notifiable))
            ->icon($this->getIcon())
            ->iconColor($this->getIconColor())
            ->actions([
                Action::make('view')
                    ->label(__('View Request'))
                    ->url(route('filament.donor.pages.blood-requests'))
                    ->button()
                    ->markAsRead(),
            ]);
    }

    private function getTitle(): string
    {
        return match ($this->bloodRequest->urgency_level) {
            UrgencyLevel::CRITICAL => __('Critical Blood Donation Request'),
            default => __('Blood Donation Request'),
        };
    }

    private function getBody(object $notifiable): string
    {
        $organization = $this->bloodRequest->organization;
        $orgName = $organization?->localized_org_name ?: __('Hospital Not Specified');
        $bloodType = $this->bloodRequest->blood_type->getLabel();
        $units = $this->bloodRequest->units_needed;

        $body = __(':org needs :units unit(s) of blood type :blood_type', [
            'org' => $orgName,
            'units' => $units,
            'blood_type' => $bloodType,
        ]);

        if ($notifiable->donor?->healthProfile?->blood_type === BloodType::UNKNOWN) {
            $body .= "\n" . __('Note: Your blood type will be determined at the hospital');
        }

        if ($this->distance !== null) {
            $distanceKm = round($this->distance, 1);
            $body .= " - " . __('Distance: :distance km', ['distance' => $distanceKm]);
        }

        return $body;
    }

    private function getIcon(): string
    {
        return match ($this->bloodRequest->urgency_level) {
            UrgencyLevel::CRITICAL => 'heroicon-o-exclamation-triangle',
            default => 'heroicon-o-heart',
        };
    }

    private function getIconColor(): string
    {
        return match ($this->bloodRequest->urgency_level) {
            UrgencyLevel::CRITICAL => 'danger',
            default => 'primary',
        };
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
