<?php

namespace App\Livewire\Cart;

use App\Services\Cart;
use App\Support\Money;
use Livewire\Component;

class Page extends Component
{
    public function updateQuantity(int $productId, int $quantity): void
    {
        app(Cart::class)->update($productId, $quantity);
        $this->dispatchCartUpdated();
    }

    public function remove(int $productId): void
    {
        app(Cart::class)->remove($productId);
        $this->dispatchCartUpdated();
    }

    public function clear(): void
    {
        app(Cart::class)->clear();
        $this->dispatchCartUpdated();
    }

    public function lines(): array
    {
        return app(Cart::class)->lines();
    }

    public function subtotal(): float
    {
        return app(Cart::class)->subtotal();
    }

    public function tax(): float
    {
        return app(Cart::class)->tax();
    }

    public function total(): float
    {
        return app(Cart::class)->total();
    }

    public function count(): int
    {
        return app(Cart::class)->unitCount();
    }

    private function dispatchCartUpdated(): void
    {
        $cart = app(Cart::class);
        $this->dispatch('cart-updated', count: $cart->unitCount(), total: Money::format($cart->total()));
    }

    public function render(): mixed
    {
        return view('livewire.cart.page');
    }
}
