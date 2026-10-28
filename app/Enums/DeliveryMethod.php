<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum DeliveryMethod: string implements HasLabel
{
    case Shipping = 'shipping';
    case Pickup = 'pickup';

    public function getLabel(): string
    {
        return match ($this) {
            self::Shipping => 'Envío a domicilio o agencia',
            self::Pickup => 'Recojo en tienda',
        };
    }
}
