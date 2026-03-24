<?php

namespace App\Mail;

use App\Models\WorkRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WorkRequestReadyForDecisionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public WorkRequest $workRequest) {}

    public function build(): static
    {
        return $this
            ->subject('[Action Required] Final Decision Needed — ' . $this->workRequest->name_of_project)
            ->view('emails.work-requests.ready-for-decision');
    }
}