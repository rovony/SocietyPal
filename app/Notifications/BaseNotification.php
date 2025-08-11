<?php

namespace App\Notifications;

use App\Models\EmailSetting;
use App\Models\GlobalSetting;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\App;
use App\Services\PushNotificationService;

class BaseNotification extends Notification implements ShouldQueue
{

    use Queueable, Dispatchable;


    protected $society = null;

    public function build(object $notifiable = null)
    {
        // Set the company and global settings

        $society = $this->society;

        $globalSetting = GlobalSetting::first();

        $locale = $notifiable->locale ?? 'en';

        // Set the application locale based on the company's locale or global settings
        if (isset($locale)) {
            App::setLocale($locale ?? (!is_null($society) ? $society->locale : 'en'));
        } else {
            App::setLocale(session('locale') ?: $globalSetting->locale);
        }

        // Retrieve SMTP settings
        $smtpSetting = EmailSetting::first();

        // Initialize a mail message instance
        $build = (new MailMessage);

        // Set default reply name and email to SMTP settings
        $replyName = $companyName = $smtpSetting->mail_from_name;
        $replyEmail = $smtpFromEmail = $smtpSetting->mail_from_email;

        // Set the application logo URL from the global settings
        Config::set('app.logo', $globalSetting->logoUrl);
        Config::set('app.name', $companyName);

        // If a society is specified, customize the reply name, email, logo URL, and application name
        if (!is_null($society)) {
            $replyName = $society->name;
            $replyEmail = $society->email;
            Config::set('app.logo', $society->logo_url);
            Config::set('app.name', $replyName);
        }

        // Ensure that the society email and name are used if mail verification is successful
        // $societyEmail = config('mail.verified') === true ? $societyEmail : $replyEmail;


        // Return the mail message with configured from and replyTo settings
        return $build->from($smtpFromEmail, $replyName)->replyTo($replyEmail, $replyName);
    }

    protected function sendPushNotification($notifiable, $message, $url = null)
    {
        try {
            dispatch(function () use ($notifiable, $message, $url) {
                (new PushNotificationService())->sendPushNotification(
                    $notifiable->id,
                    $message,
                    $url
                );
            });
        } catch (\Exception $e) {
            // Log if needed
        }
    }

}
