<?php

namespace App\Notifications;

use App\Models\Society;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrialLicenseExp extends BaseNotification
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
            ->subject(__('email.trialLicenseExp.subject') . ' - ' . $siteName . '!')
            ->greeting(__('email.trialLicenseExp.greeting', ['name' => $notifiable->name]))
            ->line(__('email.trialLicenseExp.line1'))
            ->line(__('email.trialLicenseExp.line2'))
            ->action(__('email.trialLicenseExp.action'), route('dashboard'));
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
            'message' => __('email.trialLicenseExp.subject') . ' - ' . $siteName . '!',
            'created_at' => now()->toDateTimeString(),
            'user_name' => $notifiable->name,
        ];
    }
}
