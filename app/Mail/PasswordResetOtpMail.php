<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $emailData;

    /**
     * Create a new message instance.
     */
    public function __construct(array $emailData)
    {
        $this->emailData = $emailData;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this
            ->from(
                config('mail.from.address', 'no-reply@bluecrownbarbers.com'),
                config('mail.from.name', 'Rosenthal Barbershop')
            )
            ->subject($this->emailData['subject'] ?? 'Your Password Reset OTP - Rosenthal Barbershop')
            ->view('emails.password-reset-otp');
    }
}
