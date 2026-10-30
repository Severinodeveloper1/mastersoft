<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum OrderStatus: string implements HasLabel
{
    case Pending = 'pending';
    case SentToErp = 'sent_to_erp';
    case Confirmed = 'confirmed';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'Pendiente',
            self::SentToErp => 'Enviado al ERP',
            self::Confirmed => 'Confirmado',
            self::Completed => 'Completado',
            self::Cancelled => 'Cancelado',
        };
    }
}
