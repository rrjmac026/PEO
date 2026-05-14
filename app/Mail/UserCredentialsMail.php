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
        public bool   $isResend = false,
    ) {}

    public function build(): static
    {
        $isResend = $this->isResend ?? false;

        return $this
            ->subject($isResend 
                ? '[Credentials Reset] Your New Login Credentials' 
                : '[Account Created] Your Login Credentials')
            ->view('emails.user.credentials')
            ->with([
                'user'          => $this->user,
                'plainPassword' => $this->plainPassword,
            ]);
    }
}