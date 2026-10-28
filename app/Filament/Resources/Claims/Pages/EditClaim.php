<?php

namespace App\Filament\Resources\Claims\Pages;

use App\Filament\Resources\Claims\ClaimResource;
use Filament\Resources\Pages\EditRecord;

class EditClaim extends EditRecord
{
    protected static string $resource = ClaimResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (filled($data['provider_response'] ?? null) && blank($data['responded_at'] ?? null)) {
            $data['responded_at'] = now();
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
