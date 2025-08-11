<?php

namespace App\Notifications;

use App\Models\Notice;
use App\Models\Society;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeSocietyEmail extends Notification
{
    use Queueable;

    public $society;
    /**
     * Create a new notification instance.
     */
    public function __construct(Society $society)
    {
        $this->society = $society;
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
        $siteName = global_setting()->name;

        return (new MailMessage)
        ->subject('Welcome to '.$siteName.'! Simplify Your Society Management Today!')
            ->greeting(__('app.hello') .' '. $notifiable->name . ',')
            ->line('Congratulations and welcome to '.$siteName.'! We\'re excited to have your society join our platform.')
            ->line('What\'s next?')
            ->line('**Set Up Your Society Details**: Add your society’s information, resident details, and administrative contacts effortlessly.')
            ->line('**Streamline Communication**: Share notices, announcements, and updates with residents instantly.')
           ->line('**Simplify Billing**: Manage maintenance fees, payments, and dues seamlessly.')
            ->line('**Secure Your Society**: Monitor entry/exit logs and enhance security with visitor tracking.')
            ->line('Let\'s transform your society management into a hassle-free experience!');
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

