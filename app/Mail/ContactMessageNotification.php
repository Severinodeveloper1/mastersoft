<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessageNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $contactMessage) {}

    public function build(): static
    {
        return $this->subject('Nuevo mensaje de contacto: '.$this->contactMessage->subject)
            ->view('mail.contact-message');
    }
}
