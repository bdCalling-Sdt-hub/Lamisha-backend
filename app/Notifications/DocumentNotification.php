<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\DocumentNotification;
class DocumentNotification extends Notification
{


    use Queueable;

    public $message;
    public $new_data;

    /**
     * Create a new notification instance.
     *
     * @param string $message
     * @param mixed $document
     */
    public function __construct($message, $new_data)
    {
        $this->message = $message;
        $this->new_data = $new_data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Convert the notification to an array.
     *
     * @param mixed $notifiable
     * @return array<string, mixed>
     */
    public function toArray($notifiable): array
    {
        return [
            'message' => $this->message,
            'new_data' => $this->new_data
        ];
    }
}
