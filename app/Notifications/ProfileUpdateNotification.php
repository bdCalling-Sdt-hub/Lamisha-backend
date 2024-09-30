<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProfileUpdateNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $profileUpdate;
    public function __construct($newProfileUpdate)
    {
        $this->profileUpdate = $newProfileUpdate;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

      public function toArray(object $notifiable): array
    {
        return [
            'id' => $this->profileUpdate->id,
            'user_id' => $this->profileUpdate->user_id,
            'first_name' => $this->profileUpdate->first_name,
            'last_name' => $this->profileUpdate->last_name,
            'phone' => $this->profileUpdate->phone,
            'buisness_address' => $this->profileUpdate->buisness_address,
            'buisness_name' => $this->profileUpdate->buisness_name,
            'message' => 'User profile update request',
            'created_at' => $this->profileUpdate->created_at,
        ];
    }
}
