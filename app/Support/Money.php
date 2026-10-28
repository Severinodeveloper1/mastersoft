<?php

namespace App\Support;

final class Money
{
    /**
     * Formats an amount in Peruvian soles, e.g. "S/. 1,049.00".
     */
    public static function format(string|int|float|null $amount): string
    {
        return 'S/. '.number_format((float) $amount, 2, '.', ',');
    }
}
