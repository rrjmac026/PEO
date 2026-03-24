<?php

namespace App\Mail;

use App\Models\WorkRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkRequestDecisionMadeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public WorkRequest $workRequest) {}

    public function envelope(): Envelope
    {
        $decision = ucfirst($this->workRequest->admin_decision);
        return new Envelope(
            subject: "[Work Request {$decision}] {$this->workRequest->name_of_project}",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.work-requests.decision-made');
    }
}