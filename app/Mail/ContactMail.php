<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;
    public $mailData;
    /**
     * Create a new sms instance.
     */
    public function __construct($mailData)
    {
        $this->mailData=$mailData;
    }

    /**
     * Get the sms envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Contact Mail',
        );
    }

    /**
     * Get the sms content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.contact_mail',


        );
    }

    /**
     * Get the attachments for the sms.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
