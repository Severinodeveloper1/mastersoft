<?php

namespace App\Livewire\Claims;

use App\Enums\ClaimType;
use App\Enums\DocumentType;
use App\Enums\GoodType;
use App\Mail\ClaimFiledNotification;
use App\Models\Claim;
use App\Models\CompanySetting;
use App\Support\Ubigeo;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class ClaimForm extends Component
{
    public string $documentType = 'dni';

    public string $documentNumber = '';

    public string $firstName = '';

    public string $paternalSurname = '';

    public string $maternalSurname = '';

    public string $phone = '';

    public string $email = '';

    public string $department = '';

    public string $province = '';

    public string $district = '';

    public string $address = '';

    public bool $isMinor = false;

    public string $guardianName = '';

    public string $guardianDocument = '';

    public string $goodType = 'product';

    public string $orderReference = '';

    public string $claimedAmount = '';

    public string $goodDescription = '';

    public string $claimType = 'complaint';

    public string $detail = '';

    public string $consumerRequest = '';

    public bool $privacyAccepted = false;

    public bool $emailNotificationAccepted = false;

    public ?Claim $filedClaim = null;

    public function updatedDepartment(): void
    {
        $this->province = '';
        $this->district = '';
    }

    public function updatedProvince(): void
    {
        $this->district = '';
    }

    protected function rules(): array
    {
        $documentRule = $this->documentType === 'dni' ? 'digits:8' : 'regex:/^[A-Za-z0-9]{9,12}$/';

        return [
            'documentType' => ['required', 'in:dni,ce,passport'], 'documentNumber' => ['required', $documentRule],
            'firstName' => ['required', 'string', 'max:255'], 'paternalSurname' => ['required', 'string', 'max:255'],
            'maternalSurname' => ['required', 'string', 'max:255'], 'phone' => ['required', 'regex:/^9\d{8}$/'],
            'email' => ['required', 'email', 'max:255'], 'department' => ['required', 'string'],
            'province' => ['required', 'string'], 'district' => ['required', 'string'], 'address' => ['required', 'string', 'max:500'],
            'isMinor' => ['boolean'], 'guardianName' => [$this->isMinor ? 'required' : 'nullable', 'string', 'max:255'],
            'guardianDocument' => [$this->isMinor ? 'required' : 'nullable', 'digits:8'],
            'goodType' => ['required', 'in:product,service'], 'orderReference' => ['nullable', 'string', 'max:255'],
            'claimedAmount' => ['required', 'numeric', 'min:0'], 'goodDescription' => ['required', 'string', 'max:1000'],
            'claimType' => ['required', 'in:complaint,grievance'], 'detail' => ['required', 'string', 'max:1500'],
            'consumerRequest' => ['required', 'string', 'max:800'], 'privacyAccepted' => ['accepted'],
            'emailNotificationAccepted' => ['boolean'],
        ];
    }

    public function submit(): void
    {
        $data = $this->validate();
        if (! Ubigeo::isValid($data['department'], $data['province'], $data['district'])) {
            $this->addError('district', 'Selecciona un departamento, provincia y distrito válidos.');

            return;
        }
        $claim = Claim::file([
            'document_type' => DocumentType::from($data['documentType']), 'document_number' => $data['documentNumber'],
            'first_name' => $data['firstName'], 'paternal_surname' => $data['paternalSurname'], 'maternal_surname' => $data['maternalSurname'],
            'phone' => '+51 '.$data['phone'], 'email' => $data['email'], 'department' => $data['department'],
            'province' => $data['province'], 'district' => $data['district'], 'address' => $data['address'], 'is_minor' => $data['isMinor'],
            'guardian_name' => $data['isMinor'] ? $data['guardianName'] : null, 'guardian_document' => $data['isMinor'] ? $data['guardianDocument'] : null,
            'good_type' => GoodType::from($data['goodType']), 'order_reference' => $data['orderReference'] ?: null,
            'claimed_amount' => $data['claimedAmount'], 'good_description' => $data['goodDescription'],
            'claim_type' => ClaimType::from($data['claimType']), 'detail' => $data['detail'], 'consumer_request' => $data['consumerRequest'],
            'privacy_accepted_at' => now(), 'email_notification_accepted' => $data['emailNotificationAccepted'],
        ]);
        $settings = CompanySetting::current();
        $internal = $settings->claims_email ?: $settings->email;
        Mail::to($claim->email)->send(new ClaimFiledNotification($claim));
        if ($internal && $internal !== $claim->email) {
            Mail::to($internal)->send(new ClaimFiledNotification($claim));
        }
        $this->filedClaim = $claim;
    }

    public function render()
    {
        return view('livewire.claims.claim-form', [
            'departments' => Ubigeo::departments(),
            'provinces' => $this->department ? Ubigeo::provinces($this->department) : [],
            'districts' => $this->province ? Ubigeo::districts($this->department, $this->province) : [],
            'documentTypes' => DocumentType::cases(), 'goodTypes' => GoodType::cases(), 'claimTypes' => ClaimType::cases(),
        ]);
    }
}
