<?php

namespace App\Filament\Resources\Banners;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class BannerList extends ListRecords
{
    protected static string $resource = BannerResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
