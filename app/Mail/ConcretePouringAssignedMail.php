<?php

namespace App\Mail;

use App\Models\ConcretePouring;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConcretePouringAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ConcretePouring $concretePouring,
        public string          $role,
        public bool            $isFirst,
    ) {}

    public function build(): static
    {
        $prefix = $this->isFirst ? '[Action Required]' : '[Heads Up]';

        return $this
            ->subject("{$prefix} Concrete Pouring Request Assigned — {$this->concretePouring->project_name}")
            ->view('emails.concrete-pouring.assigned');
    }
}
