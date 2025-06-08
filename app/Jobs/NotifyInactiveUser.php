<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\InactiveUserMail;

class NotifyInactiveUser implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $recipientEmail;

    public function __construct($recipientEmail)
    {
        $this->recipientEmail = $recipientEmail;
    }




public function handle()
{
    try {
        Mail::raw('You have a new message on Hexarex. Please log in to check it.', function ($message) {
            $message->to($this->recipientEmail)
                    ->subject('You’ve Been Notified from Hexarex');
        });

        Log::info("Raw email sent to: " . $this->recipientEmail);
    } catch (\Exception $e) {
        Log::error('Failed to send raw email to inactive user: ' . $e->getMessage());
    }
}

}
