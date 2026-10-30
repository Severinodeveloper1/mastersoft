<?php

namespace App\Services\Erp;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Nightly full load of categories, brands and products from the ERP.
 *
 * Everything is fetched before anything is written, so a failed request leaves the store untouched.
 * Records with an external_id that a complete load did not return are deactivated; admin-created
 * records (no external_id) and admin-only content (summary, featured flag, uploaded images) are kept.
 * The nightly schedule and the admin button share one lock, so two runs never overlap.
 */
class CatalogSync
{
    public const LOCK = 'erp-catalog-sync';

    /** Console runs have no time limit, so their lock must outlive a long full load. */
    public const LOCK_SECONDS = 3600;

    /**
     * Web runs are capped by the request time limit; a request killed by the server never reaches
     * `finally`, so its lock must expire soon after instead of blocking every sync for an hour.
     */
    public const WEB_LOCK_SECONDS = 600;

    /** Time kept back for the write transaction after the ERP download. */
    public const WRITE_MARGIN_SECONDS = 120;

    public function __construct(private readonly ErpClient $client) {}

    /**
     * @return array<string, int>
     *
     * @throws ErpException when the ERP fails, returns an empty catalog, or another run holds the lock
     */
    public function run(int $lockSeconds = self::LOCK_SECONDS): array
    {
        $lock = Cache::lock(self::LOCK, $lockSeconds);

        if (! $lock->get()) {
            Log::warning('ERP catalog sync skipped: another run is in progress.');

            throw new ErpException('Another ERP sync is already running.');
        }

        // A fatal "maximum execution time" error skips `finally` but still runs shutdown functions.
        // Release only removes the lock while this run still owns it, so the extra call is harmless.
        register_shutdown_function(fn () => $lock->release());

        try {
            $stats = $this->load(now()->addSeconds($lockSeconds - self::WRITE_MARGIN_SECONDS));
        } catch (ErpException $exception) {
            Log::error('ERP catalog sync failed; the store was not changed.', ['error' => $exception->getMessage()]);

            throw $exception;
        } finally {
            $lock->release();
        }

        Log::info('ERP catalog sync finished.', $stats);

        return $stats;
    }

    /**
     * @return array<string, int>
     */
    private function load(CarbonInterface $writeDeadline): array
    {
        $this->client->login();

        $categories = $this->flatten($this->client->list('/categorias', ['arbol' => 'true', 'estado' => 1]), 'subcategorias');
        $brands = $this->flatten($this->client->list('/marcas', ['arbol' => 'true', 'estado' => 1]), 'submarcas');
        $products = $this->client->all('/productos', ['estado' => 1]);

        // An empty answer is far more likely an ERP glitch than a real empty catalog; deactivating
        // everything on it would empty the store, so the run stops instead.
        $this->guardAgainstEmpty(Category::class, $categories, 'categories');
        $this->guardAgainstEmpty(Product::class, $products, 'products');

        // PHP's time limit ignores time spent waiting on the ERP, so a slow download could outlive
        // the lock; writing then could overlap another run, so the run stops before any write.
        if (now()->greaterThan($writeDeadline)) {
            throw new ErpException('The ERP download took too long; nothing was written to keep runs from overlapping.');
        }

        return DB::transaction(function () use ($categories, $brands, $products): array {
            $categoryIds = $this->syncNamed(Category::class, $categories, 'idcategoria');
            $brandIds = $this->syncNamed(Brand::class, $brands, 'idmarca');

            $stats = ['created' => 0, 'updated' => 0, 'unchanged' => 0, 'skipped' => 0];
            $seen = [];
            foreach ($products as $item) {
                $result = $this->syncProduct($item, $categoryIds, $brandIds);
                $stats[$result]++;
                if ($result !== 'skipped') {
                    $seen[] = (string) $item['idproducto'];
                }
            }

            $stats['deactivated'] = $this->deactivateMissing(Product::class, $seen);
            $stats['categories'] = count($categoryIds);
            $stats['brands'] = count($brandIds);

            return $stats;
        });
    }

    /**
     * @param  class-string<Category|Brand>  $model
     * @return array<string, int> ERP id => local id
     */
    private function syncNamed(string $model, array $items, string $idKey): array
    {
        $ids = [];
        foreach ($items as $item) {
            $externalId = (string) $item[$idKey];
            $record = $model::firstOrNew(['external_id' => $externalId]);
            $record->fill(['name' => trim((string) $item['descripcion']), 'is_active' => true])->save();
            $ids[$externalId] = $record->id;
        }

        $this->deactivateMissing($model, array_keys($ids));

        return $ids;
    }

    /**
     * @return 'created'|'updated'|'unchanged'|'skipped'
     */
    private function syncProduct(array $item, array $categoryIds, array $brandIds): string
    {
        $presentation = $this->salePresentation($item);
        $price = $presentation ? $this->principalPrice($presentation) : null;

        if ($price === null) {
            Log::warning('ERP product skipped: no sale presentation with a price.', ['idproducto' => $item['idproducto']]);

            return 'skipped';
        }

        $product = Product::firstOrNew(['external_id' => (string) $item['idproducto']]);
        $product->fill([
            'name' => trim((string) $item['descripcioninterna']),
            'category_id' => $categoryIds[(string) ($item['idcategoria'] ?? '')] ?? null,
            'brand_id' => $brandIds[(string) ($item['idmarca'] ?? '')] ?? null,
            'price' => $price,
            'stock' => max(0, (int) floor((float) ($item['stock_disponible'] ?? 0))),
            'unit' => $presentation['nombre'] ?? null,
            'is_active' => true,
            'external_image_urls' => $this->imageUrls($item['imagenes'] ?? []),
        ]);

        if ($description = $this->escapedText([$item['descripcionadicional'] ?? null])) {
            $product->description = $description;
        }
        if ($information = $this->escapedText([$item['detalletecnico'] ?? null, $item['especificaciontecnica'] ?? null])) {
            $product->information = $information;
        }

        $state = ! $product->exists ? 'created' : ($product->isDirty() ? 'updated' : 'unchanged');
        $product->save();

        return $state;
    }

    private function salePresentation(array $item): ?array
    {
        $presentations = $item['presentaciones'] ?? [];

        if (count($presentations) > 1) {
            Log::warning('ERP product has several presentations; using the main sale one.', [
                'idproducto' => $item['idproducto'],
                'presentations' => count($presentations),
            ]);
        }

        foreach ($presentations as $presentation) {
            if ($presentation['principalventa'] ?? false) {
                return $presentation;
            }
        }

        return $presentations[0] ?? null;
    }

    private function principalPrice(array $presentation): ?string
    {
        $prices = $presentation['precios'] ?? [];
        $price = collect($prices)->firstWhere('principal', true) ?? ($prices[0] ?? null);

        return is_numeric($price['precio'] ?? null) && (float) $price['precio'] > 0 ? (string) $price['precio'] : null;
    }

    /**
     * @return list<string>
     */
    private function imageUrls(array $images): array
    {
        return collect($images)
            ->filter(fn ($image) => ($image['idestado'] ?? 1) == 1)
            ->sortBy(fn ($image) => $image['orden'] ?? PHP_INT_MAX)
            ->pluck('url')
            ->filter(fn ($url) => is_string($url) && str_starts_with($url, 'https://') && filter_var($url, FILTER_VALIDATE_URL))
            ->values()
            ->all();
    }

    /**
     * ERP text is plain text; the store renders these fields as HTML, so it is escaped here.
     */
    private function escapedText(array $parts): ?string
    {
        $text = collect($parts)->map(fn ($part) => trim((string) $part))->filter()->implode("\n\n");

        return $text === '' ? null : nl2br(e($text));
    }

    private function flatten(array $items, string $childrenKey): array
    {
        $flat = [];
        foreach ($items as $item) {
            $flat[] = $item;
            $flat = [...$flat, ...$this->flatten($item[$childrenKey] ?? [], $childrenKey)];
        }

        return $flat;
    }

    /**
     * @param  class-string<Model>  $model
     */
    private function guardAgainstEmpty(string $model, array $items, string $label): void
    {
        if ($items === [] && $model::query()->whereNotNull('external_id')->where('is_active', true)->exists()) {
            throw new ErpException("ERP returned no {$label}; refusing to deactivate the whole catalog.");
        }
    }

    /**
     * @param  class-string<Model>  $model
     */
    private function deactivateMissing(string $model, array $seenExternalIds): int
    {
        return $model::query()
            ->whereNotNull('external_id')
            ->whereNotIn('external_id', $seenExternalIds)
            ->where('is_active', true)
            ->update(['is_active' => false, 'updated_at' => now()]);
    }
}
