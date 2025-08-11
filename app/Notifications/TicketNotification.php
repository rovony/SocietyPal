<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class TicketNotification extends BaseNotification
{

    public $ticket;
    public $role;
    public $adminName;
    public $requesterName;
    public $agentName;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket, $role, $adminName, $requesterName, $agentName)
    {
        $this->ticket = $ticket;
        $this->role = $role;
        $this->adminName = $adminName;
        $this->requesterName = $requesterName;
        $this->agentName = $agentName;
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
        switch ($this->role) {
            case 'admin':
                return (new MailMessage)
                    ->subject(__('modules.tickets.newTicketRequested'))
                    ->greeting(__('modules.tickets.hello') . ' ' . ucfirst($this->adminName) . '!')
                    ->line($this->requesterName . ' ' . __('modules.tickets.hasRequestedTicket'))
                    ->action(__('modules.tickets.viewTicket'), url('/tickets/' . $this->ticket->id));

            case 'requester':
                return (new MailMessage)
                    ->subject(__('modules.tickets.newTicketGenerated'))
                    ->greeting(__('modules.tickets.hello') . ' ' . ucfirst($this->requesterName) . '!')
                    ->line(__('modules.tickets.ticketSuccessfullyCreated') . ' ' . $this->agentName . '.')
                    ->action(__('modules.tickets.viewTicket'), url('/tickets/' . $this->ticket->id));

            case 'agent':
                return (new MailMessage)
                    ->subject(__('modules.tickets.newTicketAssigned'))
                    ->greeting(__('modules.tickets.hello') . ' ' . ucfirst($this->agentName) . '!')
                    ->line(__('modules.tickets.ticketAssignedBy') . ' ' . $this->adminName . '.')
                    ->action(__('modules.tickets.viewTicket'), url('/tickets/' . $this->ticket->id));
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $message = '';

        switch ($this->role) {
            case 'admin':
                $message = $this->requesterName . ' ' . __('modules.tickets.newTicketRequested');
                break;

            case 'requester':
                $message = __('modules.tickets.newTicketGenerated');
                break;

            case 'agent':
                $message = __('modules.tickets.newTicketAssigned');
                break;
        }

        return [
            'society_id' => $this->society->id,
            'message' => $message,
            'url' => route('tickets.index'),
            'created_at' => now()->toDateTimeString(),
            'user_name' => $notifiable->name,
        ];
    }
}
