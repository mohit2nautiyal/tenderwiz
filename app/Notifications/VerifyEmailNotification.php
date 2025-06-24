<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $url;

    public function __construct($url)
    {

        $this->url = $url;
    }

    public function via($notifiable)
    {

        return ['mail'];
    }

   public function toMail($notifiable)
{

    try {
        \Log::info('Attempting to build mail message');
        $mail = (new MailMessage)
            ->subject('Verify Your Email Address')
            ->line('Please click the button below to verify your email address.')
            ->action('Verify Email', $this->url)
            ->line('If you did not request this change, please ignore this email.');
        
        \Log::info('Mail message built successfully');
        return $mail;
        
    } catch (\Exception $e) {
        \Log::error('Mail build failed: '.$e->getMessage());
        dd('Mail error:', $e->getMessage());
    }
}
}