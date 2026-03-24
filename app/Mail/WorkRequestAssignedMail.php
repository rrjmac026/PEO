<?php

namespace App\Mail;

use App\Models\WorkRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WorkRequestAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public WorkRequest $workRequest,
        public string      $role,
        public bool        $isFirst,
    ) {}

    public function build(): static
    {
        $prefix = $this->isFirst ? '[Action Required]' : '[Heads Up]';

        return $this
            ->subject("{$prefix} Work Request Assigned — {$this->workRequest->name_of_project}")
            ->view('emails.work-requests.assigned');
    }
}