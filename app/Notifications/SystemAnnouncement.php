<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use App\Models\Announcement;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Support\HtmlString;
use App\Settings\GeneralSettings;

/**
 * System Announcement Notification
 *
 * System-wide announcements sent to all users.
 * Delivery: Database + Real-time Broadcast (Reverb) + Email (optional)
 *
 * Recipient: All Users (broadcast to all)
 */
class SystemAnnouncement extends Notification implements ShouldQueue
{
    use Queueable;

    private Announcement $announcement;

    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement;
    }

    public function via(object $notifiable): array
    {
        $channels = ['database', 'broadcast'];
        if ($this->announcement->send_via_email) {
            $channels[] = 'mail';
        }
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $settings = app(GeneralSettings::class);
        return (new MailMessage)
            ->subject($this->getMailSubject($settings))
            ->greeting($this->getMailGreeting($notifiable))
            ->line(new HtmlString((string) $this->announcement->body))
            ->action(__('View Details'), url('/'))
            ->line(__('Thank you for using :site', ['site' => $settings->site_name]));
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->buildFilamentNotification()->getDatabaseMessage();
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return $this->buildFilamentNotification()->getBroadcastMessage();
    }

    private function buildFilamentNotification(): FilamentNotification
    {
        return FilamentNotification::make()
            ->title((string) $this->announcement->title)
            ->body((string) $this->announcement->body)
            ->icon('heroicon-o-megaphone')
            ->color('primary');
    }

    private function getMailSubject(GeneralSettings $settings): string
    {
        return __('Important Announcement: :title - :site', [
            'title' => (string) $this->announcement->title,
            'site' => $settings->site_name,
        ]);
    }

    private function getMailGreeting(object $notifiable): string
    {
        return __('Hello :name', ['name' => ($notifiable->name ?? '')]);
    }
}
