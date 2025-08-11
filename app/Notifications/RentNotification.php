<?php

namespace App\Notifications;

use App\Models\Rent;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class RentNotification extends BaseNotification
{

    public $rent;

    /**
     * Create a new notification instance.
     */
    public function __construct(Rent $rent)
    {
        $this->rent = $rent;
        $this->society = $rent->society;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject(__('modules.rent.newRentCreated'))
            ->greeting(__('modules.rent.hello'))
            ->line(__('modules.rent.newRentStatus'))

            ->line(__('modules.settings.apartmentNumber') . ': ' . ucfirst($this->rent->apartment->apartment_number))
            ->line(__('modules.rent.tenantName') . ': ' . ucfirst($this->rent->tenant->user->name))
            ->when(
                $this->rent->rent_for_year,
                fn($message) =>
                $message->line(__('modules.rent.rentForYear') . ': ' . ucfirst($this->rent->rent_for_year))
            )
            ->when(
                $this->rent->rent_for_month,
                fn($message) =>
                $message->line(__('modules.rent.rentMonth') . ': ' . ucfirst($this->rent->rent_for_month))
            )
            ->line(__('modules.rent.rentAmount') . ': ' . $this->rent->rent_amount)
            ->line(__('modules.rent.status') . ': ' . ucfirst($this->rent->status))
            ->when(
                $this->rent->payment_date,
                fn($message) =>
                $message->line(__('modules.rent.paymentDate') . ': ' . $this->rent->payment_date)
            );

        return $mailMessage;
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
            'message' => __('modules.rent.newRentCreated'),
            'url' => route('rents.index'),
            'created_at' => now()->toDateTimeString(),
            'user_name' => $notifiable->name,
        ];
    }
}
