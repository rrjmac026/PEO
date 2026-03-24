<?php

namespace App\Mail;

use App\Models\WorkRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkRequestSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public WorkRequest $workRequest) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Work Request] New Submission — ' . $this->workRequest->name_of_project,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.work-requests.submitted',
        );
    }
}