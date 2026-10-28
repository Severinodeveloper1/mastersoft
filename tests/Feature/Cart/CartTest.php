<?php

namespace Tests\Feature\Cart;

use App\Models\Product;
use App\Services\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_to_cart_uses_database_price_and_clamps_quantity_to_stock(): void
    {
        $product = Product::factory()->create(['price' => 97, 'stock' => 3]);

        $response = $this->from(route('products.show', $product))->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 9,
            'price' => 0.01,
        ])->assertRedirect(route('products.show', $product));

        $this->get(route('products.show', $product))
            ->assertSee('Tu carrito (3)')
            ->assertSee('S/. 291.00')
            ->assertSee('Producto agregado al carrito')
            ->assertSee('Ver carrito');

        $cart = app(Cart::class);
        $this->assertSame(3, $cart->unitCount());
        $this->assertSame(291.0, $cart->total());
    }

    public function test_cart_drops_products_that_are_no_longer_visible_and_splits_igv(): void
    {
        $product = Product::factory()->create(['price' => 485, 'stock' => 8]);
        $cart = app(Cart::class);
        $cart->add($product->id, 1);
        $this->assertSame(411.02, $cart->subtotal());
        $this->assertSame(73.98, $cart->tax());

        $product->update(['is_active' => false]);
        $this->assertSame(0, $cart->unitCount());
        $this->assertSame([], $cart->lines());
    }

    public function test_live_cart_drawer_updates_removes_and_clears_items(): void
    {
        $product = Product::factory()->create(['stock' => 5]);
        app(Cart::class)->add($product->id, 1);

        Livewire::test('cart.drawer')
            ->call('updateQuantity', $product->id, 3)
            ->assertSee($product->name)
            ->call('remove', $product->id)
            ->assertDontSee($product->name);

        app(Cart::class)->add($product->id, 1);
        $this->get(route('cart.show'))->assertOk()->assertSee('Resumen del pedido');
        Livewire::test('cart.drawer')->call('clear');
        $this->assertSame(0, app(Cart::class)->unitCount());
        $this->get(route('cart.show'))->assertSee('Tu carrito está vacío')->assertSee('Ir a la tienda');
    }
}
