<?php

namespace App\Filament\Resources\Brands;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class BrandList extends ListRecords
{
    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
