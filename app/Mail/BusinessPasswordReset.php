<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BusinessPasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $businessAdmin;

    /**
     * Create a new message instance.
     */
    public function __construct($token, $businessAdmin)
    {
        $this->token = $token;
        $this->businessAdmin = $businessAdmin;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Your Password - The NexZen',
            from: config('mail.from.address', 'noreply@thenexzen.com'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.business-password-reset',
            with: [
                'token' => $this->token,
                'businessAdmin' => $this->businessAdmin,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}

