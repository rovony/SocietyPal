<?php

namespace App\Notifications;


use App\Models\Amenities;
use Illuminate\Notifications\Messages\MailMessage;

class AmenitiesNotification extends BaseNotification
{

    public $ameniities;

    /**
     * Create a new notification instance.
     */
    public function __construct(Amenities $ameniities)
    {
        $this->ameniities = $ameniities;
        $this->society = $ameniities->society;
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

    public function toMail(object $notifiable)
    {
        return (new MailMessage)
            ->subject(__('modules.notifications.newAmenityCreatedSubject'))
            ->greeting(__('modules.notifications.greeting') . ' 👋')
            ->line('🌟 ' . __('modules.notifications.newAmenityIntroMessage'))
            ->line('🏠 ' . __('modules.notifications.amenitiesName') . ': ' . ($this->ameniities->amenities_name ?? __('modules.notifications.notAvailable')))
            ->line('📋 ' . __('modules.notifications.amenitiesStatus') . ': ' . ($this->ameniities->status ?? __('modules.notifications.notAvailable')))
            ->line('🕒 ' . __('modules.notifications.startTime') . ': ' . ($this->ameniities->start_time === null ? 'null' : $this->ameniities->start_time))
            ->line('⏰ ' . __('modules.notifications.endTime') . ': ' . ($this->ameniities->end_time === null ? 'null' : $this->ameniities->end_time))
            ->line('⏰ ' . __('modules.notifications.slotTime') . ': ' . ($this->ameniities->slot_time === null ? 'null' : $this->ameniities->slot_time . __('modules.settings.min')))
            ->line(__('modules.notifications.thankYouNote'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $this->sendPushNotification(
            $notifiable,
            __('modules.notifications.newAmenityCreatedSubject') . ': ' . $this->ameniities->amenities_name
        );
        
        return [
            'society_id' => $this->society->id,
            'message' => __('modules.notifications.newAmenityCreatedSubject'). ': ' .$this->ameniities->amenities_name,
            'url' => route('amenities.index'),
            'created_at' => now()->toDateTimeString(),
            'user_name' => $notifiable->name,
        ];
    }
}
