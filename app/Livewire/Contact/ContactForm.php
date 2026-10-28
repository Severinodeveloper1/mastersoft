<?php

namespace App\Livewire\Contact;

use App\Mail\ContactMessageNotification;
use App\Models\CompanySetting;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class ContactForm extends Component
{
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $subject = '';

    public string $message = '';

    public bool $privacyAccepted = false;

    public bool $sent = false;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'regex:/^9\d{8}$/'],
            'subject' => ['required', 'in:Consulta de producto,Cotización,Soporte técnico,Otro'],
            'message' => ['required', 'string', 'max:500'],
            'privacyAccepted' => ['accepted'],
        ];
    }

    public function submit(): void
    {
        $data = $this->validate();
        $record = ContactMessage::create([
            'name' => $data['name'], 'email' => $data['email'], 'phone' => '+51 '.$data['phone'],
            'subject' => $data['subject'], 'message' => $data['message'], 'privacy_accepted_at' => now(),
        ]);
        $recipient = CompanySetting::current()->email;
        if ($recipient) {
            Mail::to($recipient)->send(new ContactMessageNotification($record));
        }

        $this->sent = true;
        $this->reset(['name', 'email', 'phone', 'subject', 'message', 'privacyAccepted']);
    }

    public function render()
    {
        return view('livewire.contact.contact-form');
    }
}
