<?php

namespace App\Notifications;

use App\Models\ServiceManagement;
use Illuminate\Notifications\Messages\MailMessage;

class ServiceProviderManagementNotification extends BaseNotification
{

    public $serviceManagement;


    /**
     * Create a new notification instance.
     */
    public function __construct(ServiceManagement $serviceManagement)
    {
        $this->serviceManagement = $serviceManagement;
        $this->society = $serviceManagement->society;
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
            ->subject(__('modules.notifications.newServiceCreatedSubject'))
            ->greeting(__('modules.notifications.greeting'))
            ->line(__('modules.notifications.newServiceIntroMessage'))
            ->line(__('modules.notifications.companyName') . ': ' . ($this->serviceManagement->company_name))
            ->line(__('modules.notifications.contactPersonName') . ': ' . ($this->serviceManagement->contact_person_name))
            ->line(__('modules.notifications.phoneNumber') . ': ' . ($this->serviceManagement->phone_number))
            ->line(__('modules.notifications.serviceChargeType') . ': ' . ($this->serviceManagement->serviceType->name))
            ->line(__('modules.notifications.price') . ': ' . ($this->serviceManagement->price !== null ? $this->serviceManagement->price : __('modules.notifications.notAvailable')))
            ->line(__('modules.notifications.description') . ': ' . ($this->serviceManagement->description ?? __('modules.notifications.notAvailable')))
            ->line(__('modules.notifications.status') . ': ' . (__('app.' . $this->serviceManagement->status) ?? __('modules.notifications.notAvailable')))
            ->line(__('modules.notifications.thankYouNote'))
            ->action(__('modules.notifications.websiteLink'), $this->serviceManagement->website_link ?? '#');
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
            'message' => __('modules.notifications.newServiceCreatedSubject'),
            'url' => route('service-management.index'),
            'created_at' => now()->toDateTimeString(),
            'user_name' => $notifiable->name,
        ];
    }
}
