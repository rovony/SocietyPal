<?php

namespace App\Notifications;

use App\Models\AssetIssue;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AssetIssueNotification extends Notification
{
    use Queueable;
    public $assetIssue;

    public function __construct(AssetIssue $assetIssue)
    {
        $this->assetIssue = $assetIssue;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Asset Issue Reported')
            ->line('A new asset issue has been reported.')
            ->line('Title: ' . $this->assetIssue->title)
            ->line('Priority: ' . $this->assetIssue->priority)
            ->line('Status: ' . $this->assetIssue->status)
            ->line('Thank you for your attention!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'asset_issue_id' => $this->assetIssue->id,
            'title' => $this->assetIssue->title,
            'priority' => $this->assetIssue->priority,
            'status' => $this->assetIssue->status,
        ];
    }
}
