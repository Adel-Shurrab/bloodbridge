<?php

namespace App\Notifications;

use App\Models\BloodRequest;
use App\Models\Donor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class BloodRequestMatchNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected BloodRequest $bloodRequest;
    protected ?float $distance;

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
        return ['database'];
    }

    /**
     * Get the Filament database notification representation.
     */
    public function toDatabase(object $notifiable): array
    {
        $organization = $this->bloodRequest->organization;
        $bloodType = $this->bloodRequest->blood_type->getLabel();
        $urgency = $this->bloodRequest->urgency_level->getLabel();
        $units = $this->bloodRequest->units_needed;

        $title = match ($this->bloodRequest->urgency_level->value) {
            \App\Enums\UrgencyLevel::CRITICAL->value => '🚨 طلب تبرع عاجل جداً',
            \App\Enums\UrgencyLevel::HIGH->value => '⚠️ طلب تبرع عاجل',
            default => '🩸 طلب تبرع بالدم'
        };

        $body = "يحتاج {$organization->name} إلى {$units} وحدة من فصيلة {$bloodType}";

        if ($this->distance) {
            $body .= " - البعد: " . round($this->distance, 1) . " كم";
        }

        // Return database notification data
        return [
            'title' => $title,
            'body' => $body,
            'icon' => match ($this->bloodRequest->urgency_level->value) {
                \App\Enums\UrgencyLevel::CRITICAL->value => 'heroicon-o-exclamation-triangle',
                default => 'heroicon-o-heart'
            },
            'iconColor' => match ($this->bloodRequest->urgency_level->value) {
                \App\Enums\UrgencyLevel::CRITICAL->value => 'danger',
                \App\Enums\UrgencyLevel::HIGH->value => 'warning',
                default => 'primary'
            },
            'blood_request_id' => $this->bloodRequest->id,
            'organization_name' => $organization->name,
            'blood_type' => $bloodType,
            'urgency_level' => $urgency,
            'units_needed' => $units,
            'distance' => $this->distance,
            'actions' => [
                [
                    'name' => 'view',
                    'label' => 'عرض الطلب',
                    'url' => route('filament.donor.pages.dashboard'),
                ],
                [
                    'name' => 'ignore',
                    'label' => 'تجاهل',
                ],
            ],
        ];
    }

    /**
     * Get the array representation for raw database storage.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'blood_request_id' => $this->bloodRequest->id,
            'organization_name' => $this->bloodRequest->organization->name,
            'blood_type' => $this->bloodRequest->blood_type->value,
            'urgency_level' => $this->bloodRequest->urgency_level->value,
            'units_needed' => $this->bloodRequest->units_needed,
            'distance' => $this->distance,
        ];
    }
}
