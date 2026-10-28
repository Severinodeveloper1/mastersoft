<?php

namespace Tests\Feature\Store;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCardTest extends TestCase
{
    use RefreshDatabase;

    public function test_card_shows_offer_badge_and_struck_price_only_for_real_offers(): void
    {
        $category = Category::factory()->create(['name' => 'Access Point']);
        $onSale = Product::factory()->for($category)->create([
            'name' => 'PowerBeam M5', 'price' => 380, 'regular_price' => 456,
        ]);
        $regular = Product::factory()->for($category)->create([
            'name' => 'LiteBeam 5AC', 'price' => 295, 'regular_price' => null,
        ]);

        $offerCard = (string) $this->blade('<x-store.product-card :product="$product" />', ['product' => $onSale]);
        $regularCard = (string) $this->blade('<x-store.product-card :product="$product" />', ['product' => $regular]);

        $this->assertStringContainsString('Oferta -17%', $offerCard);
        $this->assertStringContainsString('S/. 456.00', $offerCard);
        $this->assertStringContainsString('S/. 380.00', $offerCard);
        $this->assertStringContainsString('line-through', $offerCard);
        $this->assertStringContainsString('Access Point', $offerCard);
        $this->assertStringContainsString(route('products.show', $onSale), $offerCard);

        $this->assertStringNotContainsString('Oferta', $regularCard);
        $this->assertStringNotContainsString('line-through', $regularCard);
        $this->assertStringContainsString('S/. 295.00', $regularCard);
        $this->assertStringNotContainsString('SKU', $regularCard);
    }
}
