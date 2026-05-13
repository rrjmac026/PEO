<?php

namespace App\Mail;

use App\Models\Memo;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class MemoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Memo $memo,
        public User $recipient,
    ) {}

    public function build(): static
    {
        $mail = $this
            ->subject("[{$this->memo->type_label}] {$this->memo->subject}")
            ->view('emails.memo.memo')
            ->with([
                'memo'      => $this->memo,
                'recipient' => $this->recipient,
            ]);

        // Attach uploaded files using Storage facade (works correctly on queue workers)
        foreach ($this->memo->attachments ?? [] as $path) {
            if (Storage::disk('public')->exists($path)) {
                $mail->attach(
                    Storage::disk('public')->path($path),
                    ['as' => basename($path)]
                );
            }
        }

        return $mail;
    }
}