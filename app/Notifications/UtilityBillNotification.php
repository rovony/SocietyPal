<?php

namespace App\Notifications;

use App\Models\UtilityBillManagement;
use Illuminate\Notifications\Messages\MailMessage;

class UtilityBillNotification extends BaseNotification
{

    public $utilityBill;


    /**
     * Create a new notification instance.
     */
    public function __construct(UtilityBillManagement $utilityBill)
    {
        $this->utilityBill = $utilityBill;
        $this->society = $utilityBill->society;
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
        return (new MailMessage)
            ->subject(__('modules.notifications.utilityCreatedMessage'))
            ->greeting(__('modules.notifications.utilityGreetingMessage'))
            ->line(__('modules.notifications.utilityCreatedNotifiction'))
            ->line(__('modules.settings.societyApartmentNumber') . ':' . $this->utilityBill->apartment->apartment_number)
            ->line(__('modules.settings.billType') . ':' . $this->utilityBill->billType->name)
            ->line(__('modules.utilityBills.billAmount') . ':' . number_format($this->utilityBill->bill_amount, 2))
            ->line(__('modules.utilityBills.billDate') . ':' . $this->utilityBill->bill_date)
            ->line(__('modules.settings.status') . ':' . ucfirst($this->utilityBill->status))
            ->line(__('modules.utilityBills.societyName') . ':' . $this->utilityBill->society->name)
            ->line(__('modules.notifications.thankyouMessage'))
            ->line(__('modules.notifications.supportMessage'));
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
            'message' => __('modules.notifications.utilityCreatedMessage'),
            'url' => route('utilityBills.index'),
            'created_at' => now()->toDateTimeString(),
            'user_name' => $notifiable->name,
        ];
    }
}
