<?php

namespace App\Filament\Resources\Brands;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class BrandEdit extends EditRecord
{
    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
