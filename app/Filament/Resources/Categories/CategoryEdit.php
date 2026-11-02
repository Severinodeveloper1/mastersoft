<?php

namespace App\Filament\Resources\Categories;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class CategoryEdit extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
