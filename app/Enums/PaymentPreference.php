<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentPreference: string implements HasLabel
{
    case BankTransfer = 'bank_transfer';
    case CashOnDelivery = 'cash_on_delivery';

    public function getLabel(): string
    {
        return match ($this) {
            self::BankTransfer => 'Transferencia bancaria',
            self::CashOnDelivery => 'Pago contra entrega',
        };
    }
}
