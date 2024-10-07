<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\ConfirmOrderMail;
use Mail;
class SendOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $input;
    public function __construct($data)
    {
        $this->input = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to('info@findamd4me.com')->send(new ConfirmOrderMail($this->input));
    }
}
