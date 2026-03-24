<?php

namespace App\Mail;

use App\Models\WorkRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WorkRequestDecisionMadeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public WorkRequest $workRequest) {}

    public function build(): static
    {
        $decision = ucfirst($this->workRequest->admin_decision);

        return $this
            ->subject("[Work Request {$decision}] {$this->workRequest->name_of_project}")
            ->view('emails.work-requests.decision-made');
    }
}