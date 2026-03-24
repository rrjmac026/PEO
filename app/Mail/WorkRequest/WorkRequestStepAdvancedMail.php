<?php

namespace App\Mail;

use App\Models\WorkRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkRequestStepAdvancedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public WorkRequest $workRequest,
        public string      $completedByName,
        public string      $completedStep,   // e.g. "site_inspector"
        public string      $nextStepLabel,   // e.g. "Surveyor"
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Action Required] Your Review Turn — ' . $this->workRequest->name_of_project,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.work-requests.step-advanced');
    }
}