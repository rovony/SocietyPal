<?php

namespace App\Notifications;

use App\Models\Society;
use Illuminate\Bus\Queueable;

use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionExpire extends BaseNotification
{
    use Queueable;

    protected $society;
    /**
     * Create a new notification instance.
     */
    public function __construct(Society $society)
    {
        $this->society = $society;
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
        $siteName = global_setting()->name;
        $build = parent::build($notifiable);

        return $build
            ->subject(__('email.subscriptionExpire.subject') . ' - ' . $siteName . '!')
            ->greeting(__('email.subscriptionExpire.greeting', ['name' => $notifiable->name]))
            ->line(__('email.subscriptionExpire.line1'))
            ->line(__('email.subscriptionExpire.line2'))
            ->action(__('email.subscriptionExpire.action'), route('dashboard'))
            ->line(__('email.subscriptionExpire.line3'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $siteName = global_setting()->name;
        return [
            'society_id' => $this->society->id,
            'message' => __('email.subscriptionExpire.subject') . ' - ' . $siteName . '!',
            'created_at' => now()->toDateTimeString(),
            'user_name' => $notifiable->name,
        ];
    }
}
