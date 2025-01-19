<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmailSales;

class SendEmailJob implements ShouldQueue
{
    use Queueable;

    public $emailData;

    public function __construct($emailData)
    {
        $this->emailData = $emailData;
    }

    public function handle()
    {
        // Enviar email usando a função Mail do Laravel
        Mail::to($this->emailData['email'])->send(new SendEmailSales($this->emailData));
    }
}
