<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BannerPlacement: string implements HasLabel
{
    case Hero = 'hero';
    case Side = 'side';

    public function getLabel(): string
    {
        return match ($this) {
            self::Hero => 'Carrusel principal',
            self::Side => 'Banner lateral',
        };
    }
}
