<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    public function __construct(public string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('password.reset', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()]);
        $menit = config('auth.passwords.users.expire', 60);

        return (new MailMessage)
            ->subject('Atur ulang password ' . config('rayarent.nama'))
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Kami menerima permintaan untuk mengatur ulang password akun Anda.')
            ->action('Atur ulang password', $url)
            ->line("Link ini berlaku {$menit} menit.")
            ->line('Jika Anda tidak meminta ini, abaikan email ini. Password Anda tidak berubah.')
            ->salutation('Salam, ' . config('rayarent.nama'));
    }
}
