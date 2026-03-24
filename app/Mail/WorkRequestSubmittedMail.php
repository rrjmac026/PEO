<?php

namespace App\Mail;

use App\Models\WorkRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WorkRequestSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public WorkRequest $workRequest) {}

    public function build(): static
    {
        return $this
            ->subject('[Work Request] New Submission — ' . $this->workRequest->name_of_project)
            ->view('emails.work-requests.submitted');
    }
}