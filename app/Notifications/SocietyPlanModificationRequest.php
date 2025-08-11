<?php

namespace App\Notifications;

use App\Models\Society;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;


class SocietyPlanModificationRequest extends Notification
{
    use Queueable;

    private $planChange;
    private $forSociety;
    private $packageName;

    /**
     * Create a new notification instance.
     */
    public function __construct(Society $society, $offlinePlanChangeRequest)
    {
        $this->forSociety = $society;
        $this->planChange = $offlinePlanChangeRequest;
        $this->packageName = optional($this->planChange->package)->package_name ?? 'N/A';
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
        return (new MailMessage)
            ->subject(__('email.offlineRequestReview.subject', ['site_name' => $siteName]))
            ->greeting(__('email.offlineRequestReview.greeting', ['name' => $notifiable->name]))
            ->line(__('email.offlineRequestReview.line1'))
            ->line(__('email.offlineRequestReview.line2', ['society_name' => $this->forSociety->name]))
            ->line(__('email.offlineRequestReview.line3', ['package_name' => $this->packageName]))
            ->line(__('email.offlineRequestReview.line4', ['package_type' => $this->planChange->package_type]))
            ->line(__('email.offlineRequestReview.line5'))
            ->line(__('email.offlineRequestReview.line6'));
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
            'society_id' => $this->forSociety->id,
            'message' => __('email.offlineRequestReview.subject', ['site_name' => $siteName]),
            'created_at' => now()->toDateTimeString(),
            'user_name' => $notifiable->name,
        ];
    }
}
