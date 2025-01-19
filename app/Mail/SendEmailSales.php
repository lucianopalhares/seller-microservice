<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendEmailSales extends Mailable
{
    use Queueable, SerializesModels;

    public $sales;

    /**
     * Create a new message instance.
     *
     * @param array $sales
     */
    public function __construct(array $sales)
    {
        $this->sales = $sales;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Sales Report')
                    ->view('emails.sales')
                    ->with('sales', $this->sales);
    }
}
