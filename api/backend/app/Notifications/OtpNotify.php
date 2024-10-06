<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpNotify extends Notification
{
    use Queueable;
    private $enrollmentData;

    /**
     * Create a new notification instance.
     */
    public function __construct($enrollmentData)
    {
        $this->enrollmentData = $enrollmentData;
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
        log_debug("toMail", $this->enrollmentData);
        return (new MailMessage)
            ->from(env("MAIL_FROM_ADDRESS"), 'DMS')
            ->line($this->enrollmentData['body'])
            ->action($this->enrollmentData['enrollmentText'], $this->enrollmentData['url'])
            ->line($this->enrollmentData['thankyou']);
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
