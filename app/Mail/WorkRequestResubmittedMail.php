<?php

namespace App\Mail;

use App\Models\WorkRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WorkRequestResubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public WorkRequest $workRequest,
        public string $stepLabel
    ) {}

    public function build(): static
    {
        return $this
            ->subject("[Resubmitted] {$this->workRequest->name_of_project}")
            ->view('emails.work-requests.resubmitted');
    }
}