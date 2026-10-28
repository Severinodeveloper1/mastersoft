<?php

namespace Tests\Feature\Domain;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_gets_a_sequential_code_and_pending_status(): void
    {
        $first = Order::factory()->create();
        $second = Order::factory()->create();

        $this->assertSame('MS-'.str_pad((string) $first->id, 6, '0', STR_PAD_LEFT), $first->code);
        $this->assertSame('MS-'.str_pad((string) $second->id, 6, '0', STR_PAD_LEFT), $second->code);
        $this->assertSame(OrderStatus::Pending, $first->fresh()->status);
    }

    public function test_order_items_keep_a_snapshot_of_the_product(): void
    {
        $product = Product::factory()->create(['name' => 'Adaptador HDMI a VGA', 'price' => 25]);
        $order = Order::factory()->create();

        $item = $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => $product->price,
            'quantity' => 2,
            'line_total' => 50,
        ]);

        $product->delete();

        $item->refresh();
        $this->assertNull($item->product_id);
        $this->assertSame('Adaptador HDMI a VGA', $item->product_name);
        $this->assertSame('50.00', $item->line_total);
    }
}
