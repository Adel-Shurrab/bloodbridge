<?php

namespace App\Notifications;

use App\Models\BloodRequest;
use App\Enums\BloodType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Actions\Action;

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
        $orgName = $organization?->org_name ?? 'مستشفى غير محدد';
        $bloodType = $this->bloodRequest->blood_type->getLabel();
        $units = $this->bloodRequest->units_needed;

        $title = match ($this->bloodRequest->urgency_level->value) {
            \App\Enums\UrgencyLevel::CRITICAL->value => 'طلب تبرع عاجل جداً',
            default => 'طلب تبرع بالدم'
        };

        $body = "يحتاج {$orgName} إلى {$units} وحدة من فصيلة {$bloodType}";

        if ($notifiable->donor?->healthProfile?->blood_type === BloodType::UNKNOWN) {
            $body .= "\n ملاحظة: سيتم تحديد فصيلة دمك في المستشفى";
        }

        if ($this->distance) {
            $body .= " - البعد: " . round($this->distance, 1) . " كم";
        }

        return FilamentNotification::make()
            ->title($title)
            ->body($body)
            ->icon(match ($this->bloodRequest->urgency_level->value) {
                \App\Enums\UrgencyLevel::CRITICAL->value => 'heroicon-o-exclamation-triangle',
                default => 'heroicon-o-heart'
            })
            ->iconColor(match ($this->bloodRequest->urgency_level->value) {
                \App\Enums\UrgencyLevel::CRITICAL->value => 'danger',
                default => 'primary'
            })
            ->actions([
                Action::make('view')
                    ->label('عرض الطلب')
                    ->url(route('filament.donor.pages.blood-requests'))
                    ->button()
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
