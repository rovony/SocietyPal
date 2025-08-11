<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class UserCreatedNotification extends BaseNotification
{

    public $user;
    public $password;

    /**
     * Create a new notification instance.
     */
    public function __construct($user, $password)
    {
        $this->user = $user;
        $this->password = $password;
        $this->society = $user->society;
    }
    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('email.newUser.subject'))
            ->greeting(__('email.newUser.greeting') . $this->user->name)
            ->line(__('email.newUser.message'))
            ->line(__('email.newUser.email') . ' **' . $this->user->email . '**')
            ->line(__('email.newUser.password') . ' **' . $this->password . '**')
            ->action(__('email.newUser.action'), route('login'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'society_id' => $this->society->id,
            'message' => __('email.newUser.subject'),
            'url' => route('dashboard'),
            'created_at' => now()->toDateTimeString(),
            'user_name' => $notifiable->name,
        ];
    }
}
