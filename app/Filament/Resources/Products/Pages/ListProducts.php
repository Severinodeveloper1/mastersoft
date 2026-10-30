<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Services\Erp\CatalogSync;
use App\Services\Erp\ErpException;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('syncErp')
                ->label('Sincronizar con ERP')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->requiresConfirmation()
                ->modalDescription('Se cargarán ahora las categorías, marcas y productos del ERP, igual que en la sincronización nocturna.')
                ->visible(fn (): bool => ProductResource::canCreate())
                ->action(fn (CatalogSync $sync) => $this->syncErp($sync)),
            CreateAction::make(),
        ];
    }

    private function syncErp(CatalogSync $sync): void
    {
        // A full load reads every product page; keep the request alive longer than PHP's web default.
        set_time_limit(300);

        try {
            $stats = $sync->run();
        } catch (ErpException $exception) {
            Notification::make()
                ->title('No se pudo sincronizar con el ERP')
                ->body("No se modificó ningún producto. Detalle: {$exception->getMessage()}")
                ->danger()
                ->persistent()
                ->send();

            return;
        }

        Notification::make()
            ->title('Sincronización completada')
            ->body(sprintf(
                'Creados: %d, actualizados: %d, sin cambios: %d, omitidos: %d, desactivados: %d.',
                $stats['created'], $stats['updated'], $stats['unchanged'], $stats['skipped'], $stats['deactivated'],
            ))
            ->success()
            ->send();
    }
}
