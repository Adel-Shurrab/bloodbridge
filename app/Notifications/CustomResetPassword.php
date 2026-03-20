<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomResetPassword extends Notification
{
    use Queueable;

    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a new notification instance.
     *
     * @param string $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $settings = app(\App\Settings\GeneralSettings::class);

        return (new MailMessage)
            ->subject(__('Reset Password - :site', ['site' => $settings->site_name]))
            ->greeting(__('Welcome to :site,', ['site' => $settings->site_name]))
            ->line(__('We received a request to reset the password for your account associated with this email address.'))
            ->action(__('Reset Password'), url(config('app.url') . route('password.reset', $this->token, false)))
            ->line(__('This link is only valid for 60 minutes.'))
            ->line(__('If you did not request a password reset, no further action is required.'))
            ->salutation(__('Best regards, :site Team', ['site' => $settings->site_name]));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
