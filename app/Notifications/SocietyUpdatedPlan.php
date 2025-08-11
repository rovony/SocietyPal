<?php

namespace App\Notifications;

use App\Models\Package;
use App\Models\Society;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SocietyUpdatedPlan extends Notification
{
    use Queueable;
    private $package;
    private $forSociety;
    /**
     * Create a new notification instance.
     */
    public function __construct(Society $society, $packageID)
    {
        $this->forSociety = $society;
        $this->package = Package::findOrFail($packageID);
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
            ->subject(__('email.societyUpdatedPlan.subject') . ' - ' . $siteName . '!')
            ->greeting(__('email.societyUpdatedPlan.greeting', ['name' => $notifiable->name]))
            ->line(__('email.societyUpdatedPlan.line1'))
            ->line(__('email.societyUpdatedPlan.line2'))
            ->line(__('modules.society.name') . ': ' . $this->forSociety->name)
            ->line(__('modules.package.packageName') . ': ' . $this->package->package_name)
            ->line(__('email.societyUpdatedPlan.line4'))
            ->action(__('email.societyUpdatedPlan.action'), route('dashboard'));

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
            'message' => __('email.societyUpdatedPlan.subject'). ' - ' . $siteName . '!',
            'created_at' => now()->toDateTimeString(),
            'user_name' => $notifiable->name,
        ];
    }
}
