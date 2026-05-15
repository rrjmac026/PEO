<?php

namespace App\Mail;

use App\Models\Memo;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MemoMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $memoUrl;

    public function __construct(
        public Memo $memo,
        public User $recipient,
    ) {
        $reviewerRoles = [
            'site_inspector', 'surveyor', 'resident_engineer',
            'provincial_engineer', 'mtqa', 'engineeriii', 'engineeriv',
        ];

        $this->memoUrl = match (true) {
            $recipient->role === 'admin'               => route('admin.memos.show', $memo),
            $recipient->role === 'contractor'          => route('user.memos.show', $memo),
            in_array($recipient->role, $reviewerRoles) => \Illuminate\Support\Facades\Route::has('reviewer.memos.show')
                                                            ? route('reviewer.memos.show', $memo)
                                                            : route('user.memos.show', $memo),
            default                                    => route('user.memos.show', $memo),
        };
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: "[{$this->memo->type_label}] {$this->memo->subject}");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.memo.memo');
    }
}