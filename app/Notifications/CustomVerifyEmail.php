<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class CustomVerifyEmail extends VerifyEmail
{
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
            ->subject('تأكيد عنوان البريد الإلكتروني - ' . $settings->site_name)
            ->greeting('مرحباً بك في ' . $settings->site_name . '،')
            ->line('نشكرك على التسجيل معنا! يرجى النقر على الزر أدناه لتأكيد عنوان بريدك الإلكتروني وتفعيل حسابك.')
            ->action('تأكيد البريد الإلكتروني', $verificationUrl)
            ->line('إذا لم تقم بإنشاء حساب لدينا، فلا داعي لاتخاذ أي إجراء آخر.')
            ->salutation('مع تحيات فريق عمل ' . $settings->site_name);
    }
}
