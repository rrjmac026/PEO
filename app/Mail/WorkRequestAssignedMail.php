<?php

namespace App\Mail;

use App\Models\WorkRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkRequestAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public WorkRequest $workRequest,
        public string      $role,       // e.g. "Site Inspector"
        public bool        $isFirst,    // true = it's their turn now, false = queued
    ) {}

    public function envelope(): Envelope
    {
        $prefix = $this->isFirst ? '[Action Required]' : '[Heads Up]';
        return new Envelope(
            subject: "{$prefix} Work Request Assigned — {$this->workRequest->name_of_project}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.work-requests.assigned',
        );
    }
}