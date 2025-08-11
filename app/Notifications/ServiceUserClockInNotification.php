<?php

namespace App\Notifications;

use App\Models\ServiceManagement;
use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;

class ServiceUserClockInNotification extends BaseNotification
{

    protected $serviceManagement;

    /**
     * Create a new notification instance.
     */
    public function __construct($serviceManagement)
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
        $clockInTime = Carbon::parse($this->serviceManagement->clock_in_time)->timezone(timezone())->format('h:i A');
        $subject = "{$this->serviceManagement->contact_person_name} " . __('email.serviceProviderClockedIn.subject') . " {$clockInTime}";

        return (new MailMessage)
            ->subject($subject)
            ->greeting(__('email.serviceProviderClockedIn.greeting', ['name' => $notifiable->name]))
            ->line(__('email.serviceProviderClockedIn.message'))
            ->line(__('email.serviceProviderClockedIn.service_type', ['type' => $this->serviceManagement->serviceType->name]))
            ->line(__('email.serviceProviderClockedIn.contact_person', ['name' => $this->serviceManagement->contact_person_name]))
            ->line(__('email.serviceProviderClockedIn.clock_in_date', ['date' => Carbon::parse($this->serviceManagement->clock_in_date)->timezone(timezone())->format('d F Y')]))
            ->line(__('email.serviceProviderClockedIn.clock_in_time', ['time' => Carbon::parse($this->serviceManagement->clock_in_time)->timezone(timezone())->format('h:i A')]))
            ->line(__('email.serviceProviderClockedIn.added_by', ['name' => user()->name]))
            ->line(__('email.serviceProviderClockedIn.thank_you'))
            ->action(__('email.serviceProviderClockedIn.view_details'), route('service-clock-in-out.index'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $clockInTime = Carbon::parse($this->serviceManagement->clock_in_time)->timezone(timezone())->format('h:i A');
        $subject = "{$this->serviceManagement->contact_person_name} " . __('email.serviceProviderClockedIn.subject') . " {$clockInTime}";
        return [
            'society_id' => $this->society->id,
            'message' => $subject,
            'url' => route('service-clock-in-out.index'),
            'created_at' => now()->toDateTimeString(),
            'user_name' => $notifiable->name,
        ];
    }
}
