<?php

namespace App\Mail;

use App\Models\WorkRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WorkRequestReadyToPrintMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public WorkRequest $workRequest) {}

    public function build(): static
    {
        return $this
            ->subject('[Ready to Print] ' . $this->workRequest->name_of_project)
            ->view('emails.work-requests.ready-to-print');
    }
}