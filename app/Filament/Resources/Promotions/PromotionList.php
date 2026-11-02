<?php

namespace App\Filament\Resources\Promotions;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class PromotionList extends ListRecords
{
    protected static string $resource = PromotionResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
