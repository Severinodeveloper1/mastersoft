<?php

namespace Tests\Feature\Store;

use App\Models\Category;
use App\Models\CompanySetting;
use App\Models\Product;
use App\Support\Money;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_storefront_pages_render_inside_the_store_layout(): void
    {
        $this->seed(ContentSeeder::class);
        CompanySetting::current()->update([
            'phone' => '+51 942 000 111',
            'whatsapp_number' => '51942000111',
            'email' => 'ventas@example.pe',
        ]);
        Category::factory()->create(['name' => 'Access Point']);
        $product = Product::factory()->create();

        $urls = ['/', '/tienda', "/producto/{$product->slug}", '/nosotros', '/contactanos', '/carrito',
            '/libro-de-reclamaciones', '/politicas-de-privacidad', '/terminos-y-condiciones'];

        foreach ($urls as $url) {
            $this->get($url)
                ->assertOk()
                ->assertSee('Buscar producto', false)
                ->assertSee('TROYA COMPANY E.I.R.L.')
                ->assertSee('RUC: 20614007584')
                ->assertSeeText('Distribuido por Grupo VesergenPerú')
                ->assertSee('https://wa.me/51942000111', false)
                ->assertSee('Access Point')
                ->assertDontSee('S.A.C.');
        }
    }

    public function test_legacy_urls_redirect_permanently(): void
    {
        $product = Product::factory()->create(['name' => 'Kit Crimpador']);

        $this->get('/store')->assertRedirect('/tienda')->assertStatus(301);
        $this->get('/about')->assertRedirect('/nosotros')->assertStatus(301);
        $this->get('/contact')->assertRedirect('/contactanos')->assertStatus(301);
        $this->get('/cart')->assertRedirect('/carrito')->assertStatus(301);
        $this->get('/libro-reclamaciones')->assertRedirect('/libro-de-reclamaciones')->assertStatus(301);
        $this->get('/product/kit-crimpador')->assertRedirect('/producto/kit-crimpador')->assertStatus(301);
    }

    public function test_hidden_products_return_not_found(): void
    {
        $outOfStock = Product::factory()->create(['stock' => 0]);
        $inactive = Product::factory()->create(['is_active' => false]);

        $this->get("/producto/{$outOfStock->slug}")->assertNotFound();
        $this->get("/producto/{$inactive->slug}")->assertNotFound();
    }

    public function test_whatsapp_link_is_omitted_when_no_number_is_configured(): void
    {
        $this->get('/')->assertOk()->assertDontSee('https://wa.me/', false);
    }

    public function test_money_is_formatted_in_soles(): void
    {
        $this->assertSame('S/. 380.00', Money::format(380));
        $this->assertSame('S/. 1,049.00', Money::format('1049'));
        $this->assertSame('S/. 0.50', Money::format(0.5));
    }
}
