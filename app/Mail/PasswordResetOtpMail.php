<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;

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
        $logoPath = $this->emailData['shop_logo_path'] ?? null;
        if ($logoPath && is_file($logoPath) && is_readable($logoPath)) {
            $logoPart = DataPart::fromPath($logoPath)->asInline();
            $this->emailData['shop_logo_cid'] = $logoPart->getContentId();

            $this->withSymfonyMessage(function (Email $message) use ($logoPart) {
                $message->attachPart($logoPart);
            });
        }

        return $this
            ->from(
                config('mail.from.address', 'no-reply@bluecrownbarbers.com'),
                $this->emailData['shop_name'] ?? config('mail.from.name', 'Barbershop')
            )
            ->subject($this->emailData['subject'] ?? 'Password reset verification code')
            ->view('emails.password-reset-otp');
    }
}
