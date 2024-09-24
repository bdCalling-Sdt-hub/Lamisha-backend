<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class BillingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $onoarding_fee_path;
    public $ach_payment_path;
    // public $payment_date;
    public $vendor_ordering_path;
    // public $appoinment_date;
    // public $appoinment_time;

    public function __construct($email, $onoarding_fee_path, $ach_payment_path,  $vendor_ordering_path)
    {
        $this->email = $email;
        $this->onoarding_fee_path = $onoarding_fee_path;
        $this->ach_payment_path = $ach_payment_path;
        // $this->payment_date = $payment_date;
        $this->vendor_ordering_path = $vendor_ordering_path;
        // $this->appoinment_date = $appoinment_date;
        // $this->appoinment_time = $appoinment_time;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Billing Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.billing_mail',
        );
    }

    public function build()
    {
        $email = $this->view('emails.billing')
                      ->with([
                          'email' => $this->email,
                      ]);

        if ($this->onoarding_fee_path) {
            $email->attach(storage_path('app/public/' . $this->onoarding_fee_path));
        }

        if ($this->ach_payment_path) {
            $email->attach(storage_path('app/public/' . $this->ach_payment_path));
        }

        if ($this->vendor_ordering_path) {
            $email->attach(storage_path('app/public/' . $this->vendor_ordering_path));
        }

        return $email;
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
           //
        ];
    }
}
