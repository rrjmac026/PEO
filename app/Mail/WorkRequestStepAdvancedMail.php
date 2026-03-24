<?php

namespace App\Mail;

use App\Models\WorkRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WorkRequestStepAdvancedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public WorkRequest $workRequest,
        public string      $completedByName,
        public string      $completedStep,
        public string      $nextStepLabel,
    ) {}

    public function build(): static
    {
        return $this
            ->subject('[Action Required] Your Review Turn — ' . $this->workRequest->name_of_project)
            ->view('emails.work-requests.step-advanced');
    }
}