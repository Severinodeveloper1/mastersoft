<?php

namespace Tests\Feature\Erp;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CatalogSyncTest extends TestCase
{
    use RefreshDatabase;

    private const BASE = 'https://erp.test/api/v1';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.erp' => [
            'base_url' => 'https://erp.test/api/v1',
            'tenant' => 'delta',
            'username' => 'web-reader',
            'password' => 'secret',
            'application' => 'W',
            'device' => 'Mastersoft sync',
            'per_page' => 2,
            'timeout' => 5,
            'sync_time' => '03:00',
        ]]);
    }

    public function test_sync_logs_in_with_tenant_and_uses_the_token_on_every_request(): void
    {
        $this->fakeErp(products: [[$this->product(14)]]);

        $this->artisan('erp:sync-products')->assertSuccessful();

        Http::assertSent(fn (Request $request) => $request->url() === self::BASE.'/auth/login'
            && $request->method() === 'POST'
            && $request->hasHeader('X-Tenant-Id', 'delta')
            && $request['usuario'] === 'web-reader'
            && $request['clave'] === 'secret'
            && $request['aplicacion'] === 'W'
            && $request['dispositivo'] === 'Mastersoft sync');
        Http::assertSent(fn (Request $request) => str_starts_with($request->url(), self::BASE.'/productos')
            && $request->hasHeader('Authorization', 'Bearer 1|test-token')
            && $request->hasHeader('X-Tenant-Id', 'delta')
            && str_contains($request->url(), 'estado=1'));
    }

    public function test_sync_creates_categories_brands_and_products_from_the_erp(): void
    {
        $this->fakeErp(products: [[
            $this->product(14, name: 'AXE BARRA', categoryId: 8, brandId: null, price: '18.00', stock: '8.000', images: [
                ['url' => 'https://erp.test/img/b.webp', 'orden' => 2],
                ['url' => 'https://erp.test/img/a.webp', 'orden' => 1],
                ['url' => 'http://erp.test/img/insecure.webp', 'orden' => 3],
            ]),
            $this->product(15, name: 'DVR 8 CANALES', categoryId: 2, brandId: 1, price: '450.50', stock: '2.750'),
        ]]);

        $this->artisan('erp:sync-products')->assertSuccessful();

        $this->assertSame(['Bebidas', 'Desodorante', 'General', 'categoría uno'], Category::orderBy('name')->pluck('name')->all());
        $this->assertSame(['EPSON', 'hikvision'], Brand::orderBy('name')->pluck('name')->all());

        $axe = Product::where('external_id', '14')->firstOrFail();
        $this->assertSame('AXE BARRA', $axe->name);
        $this->assertSame('axe-barra', $axe->slug);
        $this->assertSame('18.00', $axe->price);
        $this->assertSame(8, $axe->stock);
        $this->assertSame('Unidad', $axe->unit);
        $this->assertTrue($axe->is_active);
        $this->assertSame('Desodorante', $axe->category->name);
        $this->assertNull($axe->brand_id);
        $this->assertSame(['https://erp.test/img/a.webp', 'https://erp.test/img/b.webp'], $axe->external_image_urls);

        $dvr = Product::where('external_id', '15')->firstOrFail();
        $this->assertSame('categoría uno', $dvr->category->name);
        $this->assertSame('hikvision', $dvr->brand->name);
        $this->assertSame(2, $dvr->stock);
        $this->assertSame('450.50', $dvr->price);
    }

    public function test_sync_reads_every_page_of_products(): void
    {
        $this->fakeErp(products: [[$this->product(1), $this->product(2)], [$this->product(3)]]);

        $this->artisan('erp:sync-products')->assertSuccessful();

        $this->assertSame(['1', '2', '3'], Product::orderBy('external_id')->pluck('external_id')->all());
        Http::assertSent(fn (Request $request) => str_contains($request->url(), 'page=2') && str_contains($request->url(), 'per_page=2'));
    }

    public function test_second_run_updates_erp_fields_and_keeps_admin_content(): void
    {
        Storage::fake('public');
        $this->fakeErp(products: [[$this->product(14, name: 'AXE BARRA', price: '18.00')]]);
        $this->artisan('erp:sync-products')->assertSuccessful();

        $product = Product::where('external_id', '14')->firstOrFail();
        $product->update(['summary' => 'Resumen del admin', 'description' => '<p>Texto del admin</p>', 'is_featured' => true]);
        $product->addMedia(UploadedFile::fake()->image('manual.jpg'))->toMediaCollection('images', 'public');

        $this->fakeErp(products: [[$this->product(14, name: 'AXE BARRA 150ML', price: '19.50', stock: '3.000')]]);
        $this->artisan('erp:sync-products')->assertSuccessful();

        $product->refresh();
        $this->assertSame(1, Product::count());
        $this->assertSame('AXE BARRA 150ML', $product->name);
        $this->assertSame('axe-barra', $product->slug);
        $this->assertSame('19.50', $product->price);
        $this->assertSame(3, $product->stock);
        $this->assertSame('Resumen del admin', $product->summary);
        $this->assertSame('<p>Texto del admin</p>', $product->description);
        $this->assertTrue($product->is_featured);
        $this->assertCount(1, $product->getMedia('images'));
    }

    public function test_erp_text_is_escaped_before_it_reaches_the_store(): void
    {
        $this->fakeErp(products: [[$this->product(14, extra: [
            'descripcionadicional' => "Línea 1\n<script>alert(1)</script>",
            'especificaciontecnica' => 'Voltaje <b>220V</b>',
        ])]]);

        $this->artisan('erp:sync-products')->assertSuccessful();

        $product = Product::where('external_id', '14')->firstOrFail();
        $this->assertStringNotContainsString('<script>', $product->description);
        $this->assertStringContainsString('&lt;script&gt;', $product->description);
        $this->assertStringContainsString('<br />', $product->description);
        $this->assertStringContainsString('Voltaje &lt;b&gt;220V&lt;/b&gt;', $product->information);
    }

    public function test_multiple_presentations_use_the_main_sale_one_and_log_a_warning(): void
    {
        Log::spy();
        $this->fakeErp(products: [[$this->product(2, presentations: [
            $this->presentation('Docena x 12', '20.00', main: false),
            $this->presentation('Unidad', '1.50', main: true),
        ])]]);

        $this->artisan('erp:sync-products')->assertSuccessful();

        $product = Product::where('external_id', '2')->firstOrFail();
        $this->assertSame('1.50', $product->price);
        $this->assertSame('Unidad', $product->unit);
        Log::shouldHaveReceived('warning')->withArgs(fn (string $message, array $context = []) => str_contains($message, 'presentations') && ($context['idproducto'] ?? null) === 2)->once();
    }

    public function test_products_without_a_usable_price_are_skipped_and_logged(): void
    {
        Log::spy();
        $this->fakeErp(products: [[
            $this->product(1, presentations: []),
            $this->product(2, presentations: [$this->presentation('Unidad', null, main: true)]),
            $this->product(3, price: '10.00'),
        ]]);

        $this->artisan('erp:sync-products')->assertSuccessful();

        $this->assertSame(['3'], Product::pluck('external_id')->all());
        Log::shouldHaveReceived('warning')->withArgs(fn (string $message, array $context = []) => str_contains($message, 'skipped'))->twice();
    }

    public function test_records_missing_from_a_complete_load_are_deactivated_and_admin_records_are_kept(): void
    {
        $this->fakeErp(products: [[$this->product(1, categoryId: 8), $this->product(2, categoryId: 6)]]);
        $this->artisan('erp:sync-products')->assertSuccessful();
        $adminProduct = Product::factory()->create(['external_id' => null, 'is_active' => true]);
        $adminCategory = Category::factory()->create(['external_id' => null, 'is_active' => true]);

        $this->fakeErp(
            products: [[$this->product(1, categoryId: 8)]],
            categories: [$this->category(8, 'Desodorante')],
            brands: [],
        );
        $this->artisan('erp:sync-products')->assertSuccessful();

        $this->assertTrue(Product::where('external_id', '1')->value('is_active'));
        $this->assertFalse(Product::where('external_id', '2')->value('is_active'));
        $this->assertFalse(Category::where('external_id', '6')->value('is_active'));
        $this->assertFalse(Brand::where('external_id', '1')->value('is_active'));
        $this->assertTrue($adminProduct->fresh()->is_active);
        $this->assertTrue($adminCategory->fresh()->is_active);
    }

    public function test_a_failed_page_writes_nothing_and_deactivates_nothing(): void
    {
        $this->fakeErp(products: [[$this->product(1), $this->product(2)]]);
        $this->artisan('erp:sync-products')->assertSuccessful();
        $before = Product::orderBy('id')->get(['external_id', 'name', 'price', 'is_active'])->toArray();

        $this->freshHttp();
        Http::fake([
            self::BASE.'/auth/login' => Http::response($this->loginResponse()),
            self::BASE.'/categorias*' => Http::response(['status' => 'success', 'data' => []]),
            self::BASE.'/marcas*' => Http::response(['status' => 'success', 'data' => []]),
            self::BASE.'/productos?*page=1&*' => Http::response($this->productsPage([$this->product(1, name: 'CAMBIADO')], 1, 2)),
            self::BASE.'/productos?*page=2&*' => Http::response(['status' => 'error'], 500),
        ]);

        $this->artisan('erp:sync-products')->assertFailed();

        $this->assertSame($before, Product::orderBy('id')->get(['external_id', 'name', 'price', 'is_active'])->toArray());
        $this->assertSame(4, Category::where('is_active', true)->whereNotNull('external_id')->count());
    }

    public function test_a_failed_login_stops_the_sync_without_calling_the_catalog(): void
    {
        Http::fake([
            self::BASE.'/auth/login' => Http::response(['status' => 'error', 'message' => 'Credenciales inválidas'], 401),
            '*' => Http::response(['status' => 'success', 'data' => []]),
        ]);

        $this->artisan('erp:sync-products')->assertFailed();

        Http::assertSentCount(1);
        $this->assertSame(0, Product::count());
    }

    public function test_sync_is_scheduled_nightly_without_overlapping(): void
    {
        $event = collect(app(Schedule::class)->events())
            ->first(fn ($event) => str_contains($event->command ?? '', 'erp:sync-products'));

        $this->assertNotNull($event);
        $this->assertSame('0 3 * * *', $event->expression);
        $this->assertTrue($event->withoutOverlapping);
    }

    public function test_store_shows_erp_images_before_admin_uploads(): void
    {
        Storage::fake('public');
        $product = Product::factory()->create(['external_image_urls' => ['https://erp.test/img/a.webp']]);
        $product->addMedia(UploadedFile::fake()->image('manual.jpg'))->toMediaCollection('images', 'public');

        $urls = $product->fresh()->imageUrls();

        $this->assertSame('https://erp.test/img/a.webp', $urls[0]);
        $this->assertCount(2, $urls);
        $this->assertSame('https://erp.test/img/a.webp', $product->fresh()->primaryImageUrl());
        $this->get(route('products.show', $product))->assertOk()->assertSee('https://erp.test/img/a.webp', false);
        $this->get(route('store.index'))->assertOk()->assertSee('https://erp.test/img/a.webp', false);
    }

    /**
     * @param  list<list<array<string, mixed>>>  $products  one list per page
     */
    private function fakeErp(array $products, ?array $categories = null, ?array $brands = null): void
    {
        $lastPage = count($products);
        $pages = [];
        foreach ($products as $index => $items) {
            $page = $index + 1;
            $pages[self::BASE."/productos?*page={$page}&*"] = Http::response($this->productsPage($items, $page, $lastPage));
        }

        $this->freshHttp();
        Http::fake([
            self::BASE.'/auth/login' => Http::response($this->loginResponse()),
            self::BASE.'/categorias*' => Http::response(['status' => 'success', 'data' => $categories ?? [
                $this->category(6, 'Bebidas'),
                $this->category(8, 'Desodorante'),
                $this->category(1, 'General', [$this->category(2, 'categoría uno')]),
            ]]),
            self::BASE.'/marcas*' => Http::response(['status' => 'success', 'data' => $brands ?? [
                ['idmarca' => 2, 'descripcion' => 'EPSON', 'imagen_url' => null, 'submarcas' => []],
                ['idmarca' => 1, 'descripcion' => 'hikvision', 'imagen_url' => null, 'submarcas' => []],
            ]]),
            ...$pages,
        ]);
    }

    /**
     * Http::fake() stacks stubs and the first match wins, so each scenario starts from a clean factory.
     */
    private function freshHttp(): void
    {
        Http::swap(new HttpFactory);
    }

    private function loginResponse(): array
    {
        return ['status' => 'success', 'message' => 'Autenticación exitosa', 'token' => '1|test-token', 'expira' => now()->addDay()->toIso8601String()];
    }

    private function productsPage(array $items, int $page, int $lastPage): array
    {
        return ['status' => 'success', 'data' => ['current_page' => $page, 'data' => $items, 'last_page' => $lastPage, 'per_page' => 2, 'total' => 0]];
    }

    private function category(int $id, string $name, array $children = []): array
    {
        return ['idcategoria' => $id, 'descripcion' => $name, 'imagen_url' => null, 'idestado' => 1, 'subcategorias' => $children];
    }

    private function presentation(string $name, ?string $price, bool $main): array
    {
        return [
            'nombre' => $name,
            'principalventa' => $main,
            'precios' => $price === null ? [] : [['precio' => $price, 'principal' => true]],
        ];
    }

    private function product(
        int $id,
        string $name = 'PRODUCTO',
        int $categoryId = 8,
        ?int $brandId = null,
        string $price = '18.00',
        string $stock = '8.000',
        array $images = [],
        ?array $presentations = null,
        array $extra = [],
    ): array {
        return [
            'idproducto' => $id,
            'codigoproducto' => sprintf('PRD%05d', $id),
            'idcategoria' => $categoryId,
            'idmarca' => $brandId,
            'descripcioninterna' => $name === 'PRODUCTO' ? "PRODUCTO {$id}" : $name,
            'descripcionadicional' => null,
            'detalletecnico' => null,
            'especificaciontecnica' => null,
            'idestado' => 1,
            'stock' => $stock,
            'stock_disponible' => $stock,
            'presentaciones' => $presentations ?? [$this->presentation('Unidad', $price, main: true)],
            'imagenes' => array_map(fn (array $image) => ['principal' => $image['orden'] === 1, 'idestado' => 1, ...$image], $images),
            ...$extra,
        ];
    }
}
