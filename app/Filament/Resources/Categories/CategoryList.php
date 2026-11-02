<?php

namespace App\Filament\Resources\Categories;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class CategoryList extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
