<?php

namespace App\Mail;

use App\Models\WorkRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkRequestReadyForDecisionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public WorkRequest $workRequest) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Action Required] Final Decision Needed — ' . $this->workRequest->name_of_project,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.work-requests.ready-for-decision');
    }
}