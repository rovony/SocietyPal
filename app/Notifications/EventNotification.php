<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventNotification extends BaseNotification
{
    public $event;

    /**
     * Create a new notification instance.
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
        $this->society = $event->society;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {

        return (new MailMessage)
        ->subject(__('email.event.subject', ['event_name' => $this->event->event_name]))
        ->greeting(__('email.event.greeting'))
        ->line(__('email.event.text', ['event_name' => $this->event->event_name]))
        ->line(__('email.event.where', ['where' => $this->event->where]))
        ->line(__('email.event.start_date_time', ['start' => $this->event->start_date_time->format('d M Y, h:i A')]))
        ->line(__('email.event.end_date_time', ['end' => $this->event->end_date_time->format('d M Y, h:i A')]))
        ->action(__('email.event.action'), url('/events/' . $this->event->id))
        ->line(__('email.event.thank_you'));

    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
