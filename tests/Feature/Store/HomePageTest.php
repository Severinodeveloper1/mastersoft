<?php

namespace Tests\Feature\Store;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Database\Seeders\DemoCatalogSeeder;
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

    public function test_home_shows_only_the_categories_marked_for_the_home_page_in_their_order(): void
    {
        $second = Category::factory()->create(['name' => 'Redes marcada', 'show_on_home' => true, 'sort_order' => 2]);
        $first = Category::factory()->create(['name' => 'Impresoras marcada', 'show_on_home' => true, 'sort_order' => 1]);
        $hidden = Category::factory()->create(['name' => 'Papeleria sin marcar', 'show_on_home' => false]);
        $empty = Category::factory()->create(['name' => 'Marcada sin productos', 'show_on_home' => true]);
        foreach ([$second, $first, $hidden] as $category) {
            Product::factory()->create(['category_id' => $category->id]);
        }

        $content = $this->get(route('home'))->assertOk()->getContent();
        $start = strpos($content, 'Compra por categoría');
        $this->assertNotFalse($start, 'The home page has no "Compra por categoría" section.');
        $section = substr($content, $start, strpos($content, '</section>', $start) - $start);

        $this->assertStringContainsString($first->name, $section);
        $this->assertStringContainsString($second->name, $section);
        $this->assertStringNotContainsString($hidden->name, $section);
        $this->assertStringNotContainsString($empty->name, $section);
        $this->assertLessThan(strpos($section, $second->name), strpos($section, $first->name));
    }

    public function test_demo_catalog_fills_the_home_category_section(): void
    {
        $this->seed(DemoCatalogSeeder::class);

        $content = $this->get(route('home'))->assertOk()->getContent();
        $start = strpos($content, 'Compra por categoría');
        $this->assertNotFalse($start, 'The home page has no "Compra por categoría" section.');
        $section = substr($content, $start, strpos($content, '</section>', $start) - $start);

        // Demo categories 1-6 are marked for the home page; the seventh ("Sensores") is not.
        $this->assertStringContainsString('Access Point', $section);
        $this->assertStringNotContainsString('Sensores', $section);
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
