<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPassword extends Notification
{
    use Queueable;
    protected $reset_password;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($reset_password)
    {
       $this->reset_password=$reset_password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
        ->greeting($this->reset_password['welcome_text'])
        ->line($this->reset_password['body'])
        ->action($this->reset_password['call_to_action'], $this->reset_password['url'])
        ->line($this->reset_password['conclusion']);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'reset_password_id'=>  $this->reset_password['id']
          ];
    }
}

