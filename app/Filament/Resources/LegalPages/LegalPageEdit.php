<?php

namespace App\Filament\Resources\LegalPages;

use Filament\Resources\Pages\EditRecord;

class LegalPageEdit extends EditRecord
{
    protected static string $resource = LegalPageResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
