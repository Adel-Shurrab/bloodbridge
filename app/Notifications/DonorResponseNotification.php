<?php

namespace App\Notifications;

use App\Models\RequestResponse;
use App\Models\BloodRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

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
            \App\Enums\RequestResponseStatus::PENDING->value => '✋ متبرع جديد وافق على التبرع',
            \App\Enums\RequestResponseStatus::ACCEPTED->value => '✅ متبرع وصل إلى المستشفى',
            \App\Enums\RequestResponseStatus::COMPLETED->value => '🎉 تبرع مكتمل',
            \App\Enums\RequestResponseStatus::DECLINED->value => '❌ تبرع مرفوض طبياً',
            \App\Enums\RequestResponseStatus::NO_SHOW->value => '⚠️ متبرع لم يحضر',
            default => '📝 استجابة متبرع'
        };

        $body = $donor->user->name . " - فصيلة الدم: ";
        $bloodType = $donor->healthProfile?->blood_type;
        $body .= $bloodType ? $bloodType->getLabel() : 'غير محدد';

        if ($this->response->distance) {
            $body .= " - " . round($this->response->distance, 1) . " كم";
        }

        return [
            'title' => $title,
            'body' => $body,
            'icon' => match ($status->value) {
                \App\Enums\RequestResponseStatus::COMPLETED->value => 'heroicon-o-check-circle',
                \App\Enums\RequestResponseStatus::DECLINED->value,
                \App\Enums\RequestResponseStatus::NO_SHOW->value => 'heroicon-o-x-circle',
                default => 'heroicon-o-user'
            },
            'iconColor' => match ($status->value) {
                \App\Enums\RequestResponseStatus::COMPLETED->value => 'success',
                \App\Enums\RequestResponseStatus::DECLINED->value,
                \App\Enums\RequestResponseStatus::NO_SHOW->value => 'danger',
                \App\Enums\RequestResponseStatus::ACCEPTED->value => 'info',
                default => 'warning'
            },
            'response_id' => $this->response->id,
            'blood_request_id' => $bloodRequest->id,
            'donor_name' => $donor->user->name,
            'status' => $status->getLabel(),
            'actions' => [
                [
                    'name' => 'view',
                    'label' => 'عرض الرد',
                    'url' => route('filament.organization.resources.blood-requests.index', [
                        'tenant' => $bloodRequest->organization->slug
                    ]),
                ],
            ],
        ];
    }
}
