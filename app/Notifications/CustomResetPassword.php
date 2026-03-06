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
            ->subject('إعادة تعيين كلمة المرور - ' . $settings->site_name)
            ->greeting('مرحباً بك في ' . $settings->site_name . '،')
            ->line('لقد تلقينا طلباً لإعادة تعيين كلمة المرور الخاصة بحسابك المرتبط بهذا البريد الإلكتروني.')
            ->action('إعادة تعيين كلمة المرور', url(config('app.url') . route('password.reset', $this->token, false)))
            ->line('هذا الرابط صالح لمدة 60 دقيقة فقط.')
            ->line('إذا لم تطلب إعادة تعيين كلمة المرور، فلا داعي لاتخاذ أي إجراء آخر.')
            ->salutation('مع تحيات فريق عمل ' . $settings->site_name);
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
