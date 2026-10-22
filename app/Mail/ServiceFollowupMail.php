<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ServiceFollowupMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $serviceName;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $serviceName)
    {
        $this->user = $user;
        $this->serviceName = $serviceName;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this
            ->from('no-reply@bluecrownbarbers.com', 'Rosenthal Barbershop')
            ->subject("Book Your Next Barber Service")
            ->view('email.reminder-appointments-upcoming')
            ->text('email.reminder-appointments-upcoming')
            ->with([
                'user' => $this->user,
                'serviceName' => $this->serviceName
            ]);
    }
}
