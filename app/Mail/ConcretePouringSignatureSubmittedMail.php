<?php

namespace App\Mail;

use App\Models\ConcretePouring;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConcretePouringSignatureSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ConcretePouring $concretePouring,
        public string          $submittedByRole,
        public string          $submittedByName,
    ) {}

    public function build(): static
    {
        return $this
            ->subject('[Concrete Pouring] ' . $this->submittedByRole . ' Signed — ' . $this->concretePouring->project_name)
            ->view('emails.concrete-pouring.signature-submitted');
    }
}
