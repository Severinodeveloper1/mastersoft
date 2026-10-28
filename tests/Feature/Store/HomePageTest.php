<?php

namespace Tests\Feature\Store;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_shows_featured_products_and_hides_out_of_stock_and_empty_sections(): void
    {
        Product::factory()->create(['name' => 'Destacado visible', 'is_featured' => true, 'category_id' => null]);
        Product::factory()->create(['name' => 'Sin stock', 'stock' => 0, 'is_featured' => true]);

        $this->get(route('home'))->assertOk()->assertSee('Destacado visible')
            ->assertDontSee('Sin stock')->assertDontSee('Compra por categoría')
            ->assertDontSee('Nuestras promociones');
    }

    public function test_home_orders_best_sellers_by_sold_quantity(): void
    {
        $first = Product::factory()->create(['name' => 'Más vendido']);
        $second = Product::factory()->create(['name' => 'Segundo vendido']);
        $order = Order::factory()->create();
        $order->items()->create(['product_id' => $first->id, 'product_name' => $first->name, 'unit_price' => 10, 'quantity' => 8, 'line_total' => 80]);
        $order->items()->create(['product_id' => $second->id, 'product_name' => $second->name, 'unit_price' => 10, 'quantity' => 3, 'line_total' => 30]);

        $response = $this->get(route('home'))->assertOk();
        $bestSellerSection = substr($response->getContent(), strpos($response->getContent(), 'Productos más vendidos'));
        $this->assertLessThan(strpos($bestSellerSection, 'Segundo vendido'), strpos($bestSellerSection, 'Más vendido'));
    }
}
