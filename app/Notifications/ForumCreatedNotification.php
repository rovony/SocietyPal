<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class ForumCreatedNotification extends BaseNotification
{
    use Queueable;

    public $forum;
    /**
     * Create a new notification instance.
     */
    public function __construct($forum)
    {
        $this->forum = $forum;
        $this->society = $forum->society;
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
        $forum = $this->forum;
        $creator = $forum->user->name;

        return (new MailMessage)
            ->subject(__('email.forum.forum_subject', ['title' => $forum->title]))
            ->greeting(__('email.forum.greeting', ['name' => $notifiable->name]))
            ->line(__('email.forum.new_discussion_intro', ['title' => $forum->title,'creator' => $creator,]))
            ->line(__('email.forum.category', ['category' => optional($forum->category)->name]))
            ->action(__('email.forum.view_discussion'), route('society-forum.show', $forum->id))
            ->line(__('email.forum.thank_you'));       
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
            'message' => __('email.forum.forum_subject', ['title' => $this->forum->title]),
            'url' => route('society-forum.show', $this->forum->id),
            'created_at' => now()->toDateTimeString(),
            'user_name' => $notifiable->name,

        ];
    }
}
