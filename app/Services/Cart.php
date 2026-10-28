<?php

namespace App\Services;

use App\Models\Product;

class Cart
{
    private const SESSION_KEY = 'store_cart';

    public function add(int $productId, int $quantity): void
    {
        $product = Product::visibleInStore()->find($productId);

        if (! $product) {
            return;
        }

        $items = session(self::SESSION_KEY, []);
        $items[$productId] = min($product->stock, max(1, (int) ($items[$productId] ?? 0) + $quantity));
        session([self::SESSION_KEY => $items]);
    }

    public function update(int $productId, int $quantity): void
    {
        $items = session(self::SESSION_KEY, []);
        $product = Product::visibleInStore()->find($productId);

        if (! $product) {
            unset($items[$productId]);
        } elseif ($quantity <= 0) {
            unset($items[$productId]);
        } else {
            $items[$productId] = min($product->stock, max(1, $quantity));
        }

        session([self::SESSION_KEY => $items]);
    }

    public function remove(int $productId): void
    {
        $items = session(self::SESSION_KEY, []);
        unset($items[$productId]);
        session([self::SESSION_KEY => $items]);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    /** @return list<array{product: Product, quantity: int, unit_price: float, line_total: float}> */
    public function lines(bool $preserveSoldOut = false): array
    {
        $items = session(self::SESSION_KEY, []);
        $query = $preserveSoldOut ? Product::query() : Product::visibleInStore();
        $products = $query->whereKey(array_keys($items))->get()->keyBy('id');
        $lines = [];

        foreach ($items as $id => $quantity) {
            $product = $products->get($id);

            if (! $product || ! $product->is_active || (! $preserveSoldOut && $product->stock < 1)) {
                unset($items[$id]);

                continue;
            }

            $quantity = (int) $quantity;
            $lines[] = [
                'product' => $product,
                'quantity' => $quantity,
                'unit_price' => (float) $product->price,
                'line_total' => round((float) $product->price * $quantity, 2),
            ];

        }

        session([self::SESSION_KEY => $items]);

        return $lines;
    }

    /**
     * Retains active sold-out products long enough for checkout to report the stock conflict.
     * Ordinary cart reads still remove products that are no longer visible in the store.
     *
     * @return list<array{product: Product, quantity: int, unit_price: float, line_total: float}>
     */
    public function checkoutLines(): array
    {
        return $this->lines(preserveSoldOut: true);
    }

    public function unitCount(): int
    {
        return array_sum(array_column($this->lines(), 'quantity'));
    }

    public function total(): float
    {
        return round(array_sum(array_column($this->lines(), 'line_total')), 2);
    }

    public function subtotal(): float
    {
        return round($this->total() / 1.18, 2);
    }

    public function tax(): float
    {
        return round($this->total() - $this->subtotal(), 2);
    }
}
