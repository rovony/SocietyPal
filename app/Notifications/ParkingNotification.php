<?php

namespace App\Notifications;

use App\Models\ParkingManagementSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class ParkingNotification extends BaseNotification
{

    public $parking;


    /**
     * Create a new notification instance.
     */
    public function __construct(ParkingManagementSetting $parking)
    {
        $this->parking = $parking;
        $this->society = $parking->society;
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
            ->subject(__('modules.notifications.parkingCreatedSubject'))
            ->greeting(__('modules.notifications.helloUser') . ' 👋')
            ->line('🚗 ' . __('modules.notifications.parkingIntroMessage'))
            ->line('🏢 ' . __('modules.notifications.apartmentDetails') . ': ' . ($this->parking->parkingCode->apartmentManagement->apartment_number ?? __('modules.notifications.notAvailable')))
            ->line('🅿️ ' . __('modules.notifications.parkingDetails') . ': ' . ($this->parking->parking_code ?? __('modules.notifications.notAvailable')))
            ->line(__('modules.notifications.thankYouNote'))
            ->salutation(__('modules.notifications.signature'));
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
            'message' => __('modules.notifications.parkingCreatedSubject'),
            'url' => route('parkings.index'),
            'created_at' => now()->toDateTimeString(),
            'user_name' => $notifiable->name,
        ];
    }
}
