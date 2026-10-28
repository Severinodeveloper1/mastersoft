<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ClaimType: string implements HasLabel
{
    case Complaint = 'complaint';
    case Grievance = 'grievance';

    public function getLabel(): string
    {
        return match ($this) {
            self::Complaint => 'Reclamo',
            self::Grievance => 'Queja',
        };
    }
}
