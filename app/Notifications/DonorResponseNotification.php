<?php

namespace App\Notifications;

use App\Models\RequestResponse;
use App\Enums\RequestResponseStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Actions\Action;

class DonorResponseNotification extends Notification implements ShouldQueue
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
        $donor = $this->response->donor;
        $bloodRequest = $this->response->bloodRequest;
        $status = $this->response->status;

        $title = match ($status->value) {
            RequestResponseStatus::PENDING->value => 'متبرع جديد وافق على التبرع',
            RequestResponseStatus::ACCEPTED->value => 'متبرع وصل إلى المستشفى',
            RequestResponseStatus::COMPLETED->value => 'تبرع مكتمل',
            RequestResponseStatus::DECLINED->value => 'تبرع مرفوض طبياً',
            RequestResponseStatus::NO_SHOW->value => 'متبرع لم يحضر',
            default => 'استجابة متبرع'
        };

        $body = $donor->user->name . " - فصيلة الدم: ";
        $bloodType = $donor->healthProfile?->blood_type;
        $body .= $bloodType ? $bloodType->getLabel() : 'غير محدد';

        if ($this->response->distance) {
            $body .= " - " . round($this->response->distance, 1) . " كم";
        }

        return FilamentNotification::make()
            ->title($title)
            ->body($body)
            ->icon(match ($status->value) {
                RequestResponseStatus::COMPLETED->value => 'heroicon-o-check-circle',
                RequestResponseStatus::DECLINED->value,
                RequestResponseStatus::NO_SHOW->value => 'heroicon-o-x-circle',
                default => 'heroicon-o-user'
            })
            ->iconColor(match ($status->value) {
                RequestResponseStatus::COMPLETED->value => 'success',
                RequestResponseStatus::DECLINED->value,
                RequestResponseStatus::NO_SHOW->value => 'danger',
                RequestResponseStatus::ACCEPTED->value => 'info',
                default => 'warning'
            })
            ->actions([
                Action::make('view')
                    ->label('عرض الرد')
                    ->url(route('filament.organization.resources.blood-requests.view', [
                        'tenant' => $bloodRequest->organization->slug,
                        'record' => $bloodRequest->id,
                    ]))
                    ->button()
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
