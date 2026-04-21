<?php

namespace App\Mail;

use App\Models\ConcretePouring;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConcretePouringApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ConcretePouring $concretePouring) {}

    public function build(): static
    {
        return $this
            ->subject('[Concrete Pouring Approved] ' . $this->concretePouring->project_name)
            ->view('emails.concrete-pouring.approved');
    }
}
