<?php

namespace App\Mail;

use App\Models\ConcretePouring;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConcretePouringReadyForDecisionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ConcretePouring $concretePouring) {}

    public function build(): static
    {
        return $this
            ->subject('[Action Required] Final Decision Needed — ' . $this->concretePouring->project_name)
            ->view('emails.concrete-pouring.ready-for-decision');
    }
}
