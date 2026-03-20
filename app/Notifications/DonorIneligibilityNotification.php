<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Filament\Notifications\Notification as FilamentNotification;

/**
 * Donor Ineligibility Notification
 *
 * Sent to donor when they're marked ineligible/excluded from donation.
 * Delivery: Database + Real-time Broadcast (Reverb)
 *
 * Recipient: Donor (User)
 */
class DonorIneligibilityNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private string $eligibilityStatus;
    private ?string $rejectionReason;
    private mixed $nextEligibleDate;
    private ?string $organizationName;

    public function __construct(
        string $eligibilityStatus,
        ?string $rejectionReason,
        mixed $nextEligibleDate,
        ?string $organizationName,
    ) {
        $this->eligibilityStatus = $eligibilityStatus;
        $this->rejectionReason   = $rejectionReason;
        $this->nextEligibleDate  = $nextEligibleDate;
        $this->organizationName  = $organizationName;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Build the notification structure.
     */
    private function buildFilamentNotification(object $notifiable): FilamentNotification
    {
        return FilamentNotification::make()
            ->title($this->getTitle())
            ->body($this->getBody())
            ->icon($this->getIcon())
            ->iconColor($this->getIconColor());
    }

    private function getTitle(): string
    {
        return $this->eligibilityStatus === 'temporary'
            ? __('Temporarily ineligible for donation')
            : __('Permanently excluded from blood donation');
    }

    private function getBody(): string
    {
        $orgName = $this->organizationName ?? __('The Organization');
        $reasonLabel = $this->getRejectionReasonLabel();

        if ($this->eligibilityStatus === 'temporary') {
            $body = __(':orgName reported that you are temporarily ineligible to donate blood', 
                ['orgName' => $orgName]);

            if ($reasonLabel !== null) {
                $body .= ' ' . __('Due to: :reason', ['reason' => $reasonLabel]);
            }

            if ($this->nextEligibleDate !== null) {
                $date = Carbon::parse($this->nextEligibleDate)->format('Y/m/d');
                $body .= '. ' . __('Expected eligibility date: :date', ['date' => $date]);
            }

            return $body;
        }

        // Permanent
        $body = __(':orgName reported your permanent exclusion from blood donation', 
            ['orgName' => $orgName]);

        if ($reasonLabel !== null) {
            $body .= ' ' . __('Due to: :reason', ['reason' => $reasonLabel]);
        }

        return $body;
    }

    private function getRejectionReasonLabel(): ?string
    {
        $rejectionLabels = [
            'low_hemoglobin' => __('Low Hemoglobin'),
            'underweight' => __('Underweight'),
            'recent_illness' => __('Recent illness / Antibiotics'),
            'low_blood_pressure' => __('Low Blood Pressure'),
            'other_temp' => __('Other temporary medical reasons'),
            'blood_virus' => __('Presence of blood viruses (HCV/HBV/HIV)'),
            'chronic_disease' => __('Chronic disease preventing donation'),
            'heart_disease' => __('Heart Diseases'),
            'cancer' => __('Medical history of cancer'),
            'other_perm' => __('Other permanent medical reasons'),
        ];

        return $this->rejectionReason 
            ? ($rejectionLabels[$this->rejectionReason] ?? $this->rejectionReason)
            : null;
    }

    private function getIcon(): string
    {
        return $this->eligibilityStatus === 'temporary'
            ? 'heroicon-o-clock'
            : 'heroicon-o-x-circle';
    }

    private function getIconColor(): string
    {
        return $this->eligibilityStatus === 'temporary' ? 'warning' : 'danger';
    }

    /**
     * Get the database notification representation.
     */
    public function toDatabase(object $notifiable): array
    {
        return $this->buildFilamentNotification($notifiable)->getDatabaseMessage();
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return $this->buildFilamentNotification($notifiable)->getBroadcastMessage();
    }
}
