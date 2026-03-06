<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Announcement;
use Filament\Notifications\Notification as FilamentNotification;

class SystemAnnouncement extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Announcement $announcement) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];
        if ($this->announcement->send_via_email) {
            $channels[] = 'mail';
        }
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        // Fallback site name
        $siteName = \Illuminate\Support\Facades\DB::table('settings')->where('name', 'site_name')->value('payload') ?? config('app.name');
        if (is_string($siteName)) {
            $siteName = json_decode($siteName, true) ?? $siteName;
        }

        return (new MailMessage)
            ->subject('إعلان هام: ' . $this->announcement->title . ' - ' . $siteName)
            ->greeting('مرحباً ' . ($notifiable->name ?? ''))
            ->line($this->announcement->body)
            ->action('عرض التفاصيل', url('/'))
            ->line('شكراً لاستخدامك ' . $siteName);
    }

    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title($this->announcement->title)
            ->body($this->announcement->body)
            ->icon('heroicon-o-megaphone')
            ->color('primary')
            ->getDatabaseMessage();
    }
}
