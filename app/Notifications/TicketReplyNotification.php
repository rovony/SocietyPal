<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class TicketReplyNotification extends BaseNotification
{

    public $ticket;
    public $replyMessage;
    public $replyUser;

    /**
     * Create a new notification instance.
     */
    public function __construct($ticket, $replyMessage, $replyUser)
    {
        $this->ticket = $ticket;
        $this->replyMessage = $replyMessage;
        $this->replyUser = $replyUser;
        $this->society = $ticket->society;
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
            ->subject(__('modules.tickets.newReplyOnTicket') . ': ' . $this->ticket->subject)
            ->greeting(__('modules.tickets.hello') . ', ' . $notifiable->name)
            ->line(__('modules.tickets.youReceivedReplyTicket') . ' ' . $this->ticket->subject . ' #' . $this->ticket->ticket_number)
            ->line(__('app.by') . ' ' . $this->ticket->user->name)
            ->action(__('modules.tickets.viewTicket'), url(route('tickets.show', $this->ticket->id)));
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
            'message' => __('modules.tickets.newReplyOnTicket') . $this->ticket->subject,
            'url' => route('tickets.index'),
            'created_at' => now()->toDateTimeString(),
            'user_name' => $notifiable->name,
        ];
    }
}
