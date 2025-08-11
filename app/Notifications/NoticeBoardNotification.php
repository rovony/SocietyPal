<?php

namespace App\Notifications;

use App\Models\Notice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use App\Services\PushNotificationService;

class NoticeBoardNotification extends BaseNotification
{

    public $notice;
    /**
     * Create a new notification instance.
     */
    public function __construct(Notice $notice)
    {
        $this->notice = $notice;
        $this->society = $notice->society;
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
            ->subject(__('modules.notice.noticeSubject', ['title' => $this->notice->title]))
            ->greeting(__('modules.notice.greeting', ['name' => $notifiable->name]))
            ->line(__('modules.notice.newNotice'))
            ->line(__('modules.notice.noticeTitle', ['title' => $this->notice->title]))
            ->action(__('modules.notice.viewNoticeBoard'), route('notices.index'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $this->sendPushNotification(
            $notifiable,
            __('modules.notice.noticeSubject', ['title' => $this->notice->title])
        );
        return [
            'society_id' => $this->society->id,
            'title' => $this->notice->title,
            'message' => __('modules.notice.noticeSubject', ['title' => $this->notice->title]),
            'url' => route('notices.index'),
            'created_at' => now()->toDateTimeString(),
            'user_name' => $notifiable->name,
        ];
    }
}
