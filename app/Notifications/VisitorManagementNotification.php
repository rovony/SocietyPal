<?php

namespace App\Notifications;

use App\Models\VisitorManagement;
use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;

class VisitorManagementNotification extends BaseNotification
{
    public $visitor;

    /**
     * Create a new notification instance.
     */
    public function __construct(VisitorManagement $visitor)
    {
        $this->visitor = $visitor;
        $this->society = $visitor->society;
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
            ->subject(__('email.visitor.newVisitorApprovalNeeded'))
            ->greeting(__('email.visitor.hello', ['name' => $this->visitor->apartment->user->name]))
            ->line(__('email.visitor.visitorApprovalMessage'))
            ->line(__('email.visitor.visitorName', ['name' => $this->visitor->visitor_name ?? '--']))
            ->line(__('email.visitor.visitorMobile', ['mobile' => $this->visitor->phone_number ?? '--']))
            ->line(__('email.visitor.visitDate', [
                'date' => $this->visitor->date_of_visit
                    ? Carbon::parse($this->visitor->date_of_visit)->format('d-m-Y')
                    : '--'
            ]))
            ->line(__('email.visitor.reviewVisitor'))
            ->action(__('email.visitor.allowDenyVisitor'), route('visitors.index'));
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $title = __('email.visitor.newVisitorApprovalNeeded');
        $visitorName = $this->visitor->visitor_name ?? '--';
        $url = url('/visitors/approval/' . $this->visitor->id);
        $message = "$title - A new visitor, $visitorName, is awaiting your approval. Tap to review and take action.";

        // Fix: send as plain string or structured args
        $this->sendPushNotification(
            $notifiable,
            $message,
            $url
        );
        return [
            'society_id' => $this->society->id,
            'message' => __('email.visitor.newVisitorApprovalNeeded'),
            'url' => route('visitors.index'),
            'created_at' => now()->toDateTimeString(),
            'user_name' => $notifiable->name,
        ];
    }
}
