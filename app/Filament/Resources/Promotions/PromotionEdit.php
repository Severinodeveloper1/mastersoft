<?php

namespace App\Filament\Resources\Promotions;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class PromotionEdit extends EditRecord
{
    protected static string $resource = PromotionResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
