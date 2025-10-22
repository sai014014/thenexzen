<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BusinessRegistrationOTP extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $businessName;
    public $adminName;

    /**
     * Create a new message instance.
     */
    public function __construct($otp, $businessName, $adminName)
    {
        $this->otp = $otp;
        $this->businessName = $businessName;
        $this->adminName = $adminName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Business Registration OTP - The NexZen',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.business-registration-otp',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
