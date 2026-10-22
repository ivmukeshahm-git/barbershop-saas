<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class invoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoices;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($invoices)
    {
        $this->invoices = $invoices;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from('no-reply@bluecrownbarbers.com', 'Rosenthal Barbershop')
            ->subject("Rosenthal Barbershop Invoice")
            ->view('email.invoice')
            ->text('email.invoice');
    }
}
