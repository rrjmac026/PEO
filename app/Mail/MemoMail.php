<?php

namespace App\Mail;

use App\Models\Memo;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

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
            ->view('emails.memo.memo');

        // Attach any uploaded files
        foreach ($this->memo->attachments ?? [] as $path) {
            $fullPath = storage_path('app/public/' . ltrim($path, '/'));
            if (file_exists($fullPath)) {
                $mail->attach($fullPath);
            }
        }

        return $mail;
    }
}