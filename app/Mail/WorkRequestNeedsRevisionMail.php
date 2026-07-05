<?php

namespace App\Mail;

use App\Models\WorkRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WorkRequestNeedsRevisionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public WorkRequest $workRequest,
        public string $stepLabel
    ) {}

    public function build(): static
    {
        return $this
            ->subject("[Revision Requested] {$this->workRequest->name_of_project}")
            ->view('emails.work-requests.needs-revision');
    }
}