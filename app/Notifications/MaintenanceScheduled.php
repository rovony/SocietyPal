<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Models\AssetMaintenance;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class MaintenanceScheduled extends Notification
{
    use Queueable;

    public $maintenance;

    public function __construct(AssetMaintenance $maintenance)
    {
        $this->maintenance = $maintenance;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Maintenance Scheduled')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new maintenance has been scheduled for your apartment.')
            ->line('**Maintenance Date:** ' . $this->maintenance->maintenance_date->format('d M, Y'))
            ->line('**Status:** ' . ucfirst($this->maintenance->status))
            ->line('Thank you for using our service.');
    }
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            
        ];
    }
}
