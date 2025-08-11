<?php

namespace App\Notifications;

use App\Models\Society;
use Illuminate\Notifications\Messages\MailMessage;

class NewSocietySignup extends BaseNotification
{

    public $forSociety;

    /**
     * Create a new notification instance.
     */
    public function __construct(Society $society)
    {
        // this is done so as url and log do not changes to society
        $this->forSociety = $society;
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
        $build = parent::build($notifiable);

        $siteName = global_setting()->name;

        return $build
            ->subject('New Society Signup on ' . $siteName . '! 🎉')
            ->greeting(__('app.hello') . ' ' . $notifiable->name . ',')
            ->line('We\'re excited to inform you that a new society has just signed up for ' . $siteName . '! 🎉')
            ->line('Society Name: ' . $this->forSociety->name);
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
