<?php

namespace App\Notifications;

use App\Models\ServiceManagement;
use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;

class ServiceUserClockOutNotification extends BaseNotification
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
        $clockOutTime = Carbon::parse($this->serviceManagement->clock_out_time)->timezone(timezone())->format('h:i A');
        $subject = "{$this->serviceManagement->contact_person_name} " . __('email.serviceProviderClockedOut.subject') . " {$clockOutTime}";

        return (new MailMessage)
            ->subject($subject)
            ->greeting(__('email.serviceProviderClockedOut.greeting', ['name' => $notifiable->name]))
            ->line(__('email.serviceProviderClockedOut.message'))
            ->line(__('email.serviceProviderClockedOut.service_type', ['type' => $this->serviceManagement->serviceType->name]))
            ->line(__('email.serviceProviderClockedOut.contact_person', ['name' => $this->serviceManagement->contact_person_name]))
            ->line(__('email.serviceProviderClockedOut.clock_out_date', ['date' => Carbon::parse($this->serviceManagement->clock_out_date)->timezone(timezone())->format('d F Y')]))
            ->line(__('email.serviceProviderClockedOut.clock_out_time', ['time' => Carbon::parse($this->serviceManagement->clock_out_time)->timezone(timezone())->format('h:i A')]))
            ->line(__('email.serviceProviderClockedOut.added_by', ['name' => user()->name]))
            ->line(__('email.serviceProviderClockedOut.thank_you'))
            ->action(__('email.serviceProviderClockedOut.view_details'), route('service-clock-in-out.index'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $clockOutTime = Carbon::parse($this->serviceManagement->clock_out_time)->timezone(timezone())->format('h:i A');
        $subject = "{$this->serviceManagement->contact_person_name} " . __('email.serviceProviderClockedOut.subject') . " {$clockOutTime}";
        return [
            'society_id' => $this->society->id,
            'message' => $subject,
            'url' => route('service-clock-in-out.index'),
            'created_at' => now()->toDateTimeString(),
            'user_name' => $notifiable->name,
        ];
    }
}
