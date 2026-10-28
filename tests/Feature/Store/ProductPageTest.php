<?php

namespace Tests\Feature\Store;

use App\Models\Category;
use App\Models\CompanySetting;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_detail_shows_offer_and_whatsapp_without_sku_and_low_stock_only_at_threshold(): void
    {
        CompanySetting::current()->update(['whatsapp_number' => '51999999999']);
        $sale = Product::factory()->create(['name' => 'Equipo de prueba', 'price' => 80, 'regular_price' => 100, 'stock' => 5]);
        $response = $this->get(route('products.show', $sale))->assertOk()->assertSee('Oferta -20%')
            ->assertSee('Ahorras S/. 20.00')->assertSee('¡Últimas 5 unidades!')->assertSee('Equipo de prueba')->assertDontSee('SKU');
        $this->assertStringContainsString(rawurlencode('Equipo de prueba'), $response->getContent());

        $regular = Product::factory()->create(['regular_price' => null, 'stock' => 6]);
        $this->get(route('products.show', $regular))->assertOk()->assertDontSee('Oferta')->assertDontSee('¡Últimas');
    }

    public function test_detail_related_products_exclude_current_and_hidden_products_and_hidden_product_is_404(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->for($category)->create(['name' => 'Principal']);
        $related = Product::factory()->for($category)->create(['name' => 'Relacionado']);
        $hidden = Product::factory()->for($category)->create(['name' => 'Oculto', 'stock' => 0]);
        $this->get(route('products.show', $product))->assertOk()->assertSee('Relacionado')->assertDontSee('Oculto');
        $this->get(route('products.show', $hidden))->assertNotFound();
    }
}
