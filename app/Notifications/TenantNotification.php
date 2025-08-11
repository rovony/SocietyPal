<?php

namespace App\Notifications;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class TenantNotification extends BaseNotification
{

    public $tenant;
    public $password;

    /**
     * Create a new notification instance.
     */
    public function __construct(Tenant $tenant, $password)
    {
        $this->tenant = $tenant;
        $this->password = $password;
        $this->society = $tenant->society;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
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
            ->greeting(__('email.newUser.greeting') . $this->tenant->user->name)
            ->line(__('email.newUser.message'))
            ->line(__('email.newUser.email') . ' **' . $this->tenant->user->email . '**')
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
