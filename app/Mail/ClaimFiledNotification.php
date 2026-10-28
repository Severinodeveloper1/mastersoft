<?php

namespace App\Mail;

use App\Models\Claim;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClaimFiledNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Claim $claim) {}

    public function build(): static
    {
        return $this->subject('Reclamo registrado – Hoja N.° '.$this->claim->code)
            ->view('mail.claim-filed');
    }
}
