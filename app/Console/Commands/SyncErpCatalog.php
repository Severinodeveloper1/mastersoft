<?php

namespace App\Console\Commands;

use App\Services\Erp\CatalogSync;
use App\Services\Erp\ErpException;
use Illuminate\Console\Command;

class SyncErpCatalog extends Command
{
    protected $signature = 'erp:sync-products';

    protected $description = 'Load categories, brands and products from the ERP (full load).';

    public function handle(CatalogSync $sync): int
    {
        try {
            $stats = $sync->run();
        } catch (ErpException $exception) {
            $this->error("ERP sync failed, nothing was changed: {$exception->getMessage()}");

            return self::FAILURE;
        }

        $this->info(sprintf(
            'Categories: %d, brands: %d. Products created: %d, updated: %d, unchanged: %d, skipped: %d, deactivated: %d.',
            $stats['categories'], $stats['brands'], $stats['created'], $stats['updated'], $stats['unchanged'], $stats['skipped'], $stats['deactivated'],
        ));

        return self::SUCCESS;
    }
}
