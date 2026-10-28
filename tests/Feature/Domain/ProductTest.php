<?php

namespace Tests\Feature\Domain;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_is_on_sale_only_when_regular_price_is_greater_than_price(): void
    {
        $onSale = Product::factory()->create(['price' => 380, 'regular_price' => 456]);
        $noRegularPrice = Product::factory()->create(['price' => 380, 'regular_price' => null]);
        $sameRegularPrice = Product::factory()->create(['price' => 380, 'regular_price' => 380]);
        $lowerRegularPrice = Product::factory()->create(['price' => 380, 'regular_price' => 300]);

        $this->assertTrue($onSale->isOnSale());
        $this->assertSame('76.00', $onSale->savings());
        $this->assertSame(17, $onSale->discountPercentage());

        foreach ([$noRegularPrice, $sameRegularPrice, $lowerRegularPrice] as $product) {
            $this->assertFalse($product->isOnSale());
            $this->assertNull($product->savings());
            $this->assertNull($product->discountPercentage());
        }
    }

    public function test_slug_is_generated_from_name_and_kept_unique(): void
    {
        $first = Product::factory()->create(['name' => 'Ubiquiti PowerBeam M5', 'slug' => null]);
        $second = Product::factory()->create(['name' => 'Ubiquiti PowerBeam M5', 'slug' => null]);

        $this->assertSame('ubiquiti-powerbeam-m5', $first->slug);
        $this->assertSame('ubiquiti-powerbeam-m5-2', $second->slug);
        $this->assertSame('slug', $first->getRouteKeyName());
    }

    public function test_storefront_scope_lists_only_active_products_in_stock(): void
    {
        $visible = Product::factory()->create(['stock' => 3, 'is_active' => true]);
        Product::factory()->create(['stock' => 0, 'is_active' => true]);
        Product::factory()->create(['stock' => 5, 'is_active' => false]);

        $this->assertSame([$visible->id], Product::query()->visibleInStore()->pluck('id')->all());
    }

    public function test_product_belongs_to_category_and_brand(): void
    {
        $category = Category::factory()->create(['name' => 'Access Point']);
        $brand = Brand::factory()->create(['name' => 'Ubiquiti']);
        $product = Product::factory()->for($category)->for($brand)->create();

        $this->assertTrue($product->category->is($category));
        $this->assertTrue($product->brand->is($brand));
        $this->assertSame('access-point', $category->slug);
        $this->assertSame(1, $category->products()->count());
    }
}
