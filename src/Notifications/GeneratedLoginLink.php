<?php

namespace GIS\UserManagement\Notifications;

use GIS\UserManagement\Models\LoginLink;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GeneratedLoginLink extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(LoginLink $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(LoginLink $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Одноразовый вход на сайт')
            ->line('Вы получили это письмо потому что Мы получили запрос на одноразовый вход на сайт.')
            ->action('Войти', route('auth.email-authenticate',['token' => $notifiable->id]))
            ->line('Если Вы не отправляли запроса, игнорируйте это письмо.');
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
