<?php

namespace App\Filament\Resources\Claims\Schemas;

use App\Enums\ClaimStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ClaimForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('status')->label('Estado')->options(collect(ClaimStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()]))->required(),
            Textarea::make('provider_response')->label('Respuesta del proveedor')->rows(8)->columnSpanFull(),
            DateTimePicker::make('responded_at')->label('Fecha de respuesta')->seconds(false),
        ]);
    }
}
