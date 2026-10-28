<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ClaimStatus: string implements HasLabel
{
    case Received = 'received';
    case InProgress = 'in_progress';
    case Answered = 'answered';

    public function getLabel(): string
    {
        return match ($this) {
            self::Received => 'Recibido',
            self::InProgress => 'En atención',
            self::Answered => 'Respondido',
        };
    }
}
