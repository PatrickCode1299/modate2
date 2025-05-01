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
            // You can add logic here to check if user is active
            // For now, we assume they're inactive and send mail

            Mail::to($this->recipientEmail)->send(new InactiveUserMail($this->recipientEmail));
        } catch (\Exception $e) {
            // Log error but don't crash anything
          //  \Log::error('Failed to send mail to inactive user: ' . $e->getMessage());
        }
    }
}
