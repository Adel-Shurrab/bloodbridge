<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CustomVerifyEmail extends VerifyEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        $settings = app(\App\Settings\GeneralSettings::class);

        return (new MailMessage)
            ->subject(__('Email Address Verification - :site', ['site' => $settings->site_name]))
            ->greeting(__('Welcome to :site,', ['site' => $settings->site_name]))
            ->line(__('Thank you for registering! Please click the button below to verify your email address and activate your account.'))
            ->action(__('Verify Email Address'), $verificationUrl)
            ->line(__('If you did not create an account, no further action is required.'))
            ->salutation(__('Best regards, :site Team', ['site' => $settings->site_name]));
    }
}
