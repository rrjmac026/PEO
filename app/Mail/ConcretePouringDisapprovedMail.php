<?php

namespace App\Mail;

use App\Models\ConcretePouring;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConcretePouringDisapprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ConcretePouring $concretePouring) {}

    public function build(): static
    {
        return $this
            ->subject('[Concrete Pouring Disapproved] ' . $this->concretePouring->project_name)
            ->view('emails.concrete-pouring.disapproved');
    }
}
