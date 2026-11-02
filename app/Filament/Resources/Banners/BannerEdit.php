<?php

namespace App\Filament\Resources\Banners;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class BannerEdit extends EditRecord
{
    protected static string $resource = BannerResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
