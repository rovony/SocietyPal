<?php

namespace App\Notifications;

use App\Models\MaintenanceManagement;
use Illuminate\Notifications\Messages\MailMessage;

class MaintenancePublishedNotification extends BaseNotification
{

    public $maintenance;
    public $cost;

    /**
     * Create a new notification instance.
     */
    public function __construct(MaintenanceManagement $maintenance, $cost)
    {
        $this->maintenance = $maintenance;
        $this->society = $maintenance->society;
        $this->cost = $cost;
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
        $maintenance = $this->maintenance;

        return (new MailMessage)
            ->subject(__('email.newMaintenance.subject'))
            ->greeting(__('modules.maintenance.greeting', ['name' => $notifiable->name]))
            ->line(__('email.newMaintenance.message'))
            ->line(__('app.month') . ' : ' . ucfirst($maintenance->month))
            ->line(__('app.year') . ' : ' . $maintenance->year)
            ->line(__('modules.maintenance.additionalCost') . ' : ' . number_format($maintenance->total_additional_cost, 2))
            ->line(__('modules.maintenance.totalAmount') . ' : ' . number_format($this->cost, 2))
            ->action(__('email.newMaintenance.action'), route('maintenance.index'));
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
            'message' => __('email.newMaintenance.subject'),
            'url' => route('maintenance.index'),
            'created_at' => now()->toDateTimeString(),
            'user_name' => $notifiable->name,
        ];
    }
}
