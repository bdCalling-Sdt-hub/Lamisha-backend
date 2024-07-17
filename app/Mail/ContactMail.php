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

    public $first_name;
    public $last_name;
    public $phone;
    public $subject;
    public $email;
    public $sms;

    /**
     * Create a new sms instance.
     */
    public function __construct($first_name, $last_name, $phone, $subject, $email, $sms)
    {
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->phone = $phone;
        $this->subject = $subject;
        $this->email = $email;
        $this->sms = $sms;
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
