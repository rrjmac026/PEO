<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User   $user,
        public string $plainPassword,
    ) {}

    public function build(): static
    {
        return $this
            ->subject('[Account Created] Your Login Credentials')
            ->view('emails.user.credentials');
    }
}