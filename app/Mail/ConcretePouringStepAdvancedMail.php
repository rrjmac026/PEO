<?php

namespace App\Mail;

use App\Models\ConcretePouring;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConcretePouringStepAdvancedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ConcretePouring $concretePouring,
        public string          $completedByName,
        public string          $completedStep,
        public string          $nextStepLabel,
    ) {}

    public function build(): static
    {
        return $this
            ->subject('[Action Required] Your Review Turn — ' . $this->concretePouring->project_name)
            ->view('emails.concrete-pouring.step-advanced');
    }
}
