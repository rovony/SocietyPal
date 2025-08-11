<?php

namespace App\Notifications;

use App\Models\CommonAreaBills;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class CommonAreaBillNotification extends BaseNotification
{

    public $commonAreaBill;


    /**
     * Create a new notification instance.
     */
    public function __construct(CommonAreaBills $commonAreaBill)
    {
        $this->commonAreaBill = $commonAreaBill;
        $this->society = $commonAreaBill->society;
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
            ->subject(__('modules.notifications.commonAreaBillCreatedMessage'))
            ->greeting(__('modules.notifications.utilityGreetingMessage'))
            ->line(__('modules.notifications.commonAreaBillCreatedNotifiction'))
            ->line(__('modules.settings.billType') . ':' . $this->commonAreaBill->billType->name)
            ->line(__('modules.utilityBills.billAmount') . ':' . number_format($this->commonAreaBill->bill_amount, 2))
            ->line(__('modules.utilityBills.billDate') . ':' . $this->commonAreaBill->bill_date->format('d-m-y'))
            ->line(__('modules.settings.status') . ':' . ucfirst($this->commonAreaBill->status))
            ->line(__('modules.utilityBills.societyName') . ':' . $this->commonAreaBill->society->name)
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
            'message' => __('modules.notifications.commonAreaBillCreatedMessage'),
            'url' => route('common-area-bill.index'),
            'created_at' => now()->toDateTimeString(),
            'user_name' => $notifiable->name,
        ];
    }
}
