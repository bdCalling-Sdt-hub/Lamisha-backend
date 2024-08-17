<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $description;
    public $filePaths;

    public function __construct($title, $description, $filePaths)
    {
        $this->title = $title;
        $this->description = $description;
        $this->filePaths = $filePaths;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Qa Mail',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.qamail',
        );
    }

    public function build()
    {
        return $this->view('emails.qa')
                    ->with([
                        'title' => $this->title,
                        'description' => $this->description,
                        'filePaths' => $this->filePaths
                    ]);
    }

    public function attachments(): array
    {
        return [];
    }
}
