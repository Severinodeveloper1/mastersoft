@props(['count' => null, 'total' => null])

@php
    $cart = app(\App\Services\Cart::class);
    $count = $cart->unitCount();
    $total = $cart->total();
@endphp

<a href="{{ route('cart.show') }}" x-data="{ count: @js($count), total: @js(\App\Support\Money::format($total)) }" @cart-updated.window="count = $event.detail.count; total = $event.detail.total" class="relative flex items-center gap-2 rounded-full bg-surface-alt px-3 py-1.5 hover:bg-line" aria-label="Carrito de compras">
    <span class="relative">
        <x-heroicon-o-shopping-cart class="size-6" />
        <span class="absolute -top-1.5 -right-1.5 flex size-4 items-center justify-center rounded-full bg-accent text-[10px] font-bold text-white" x-text="count">{{ $count }}</span>
    </span>
    <span class="hidden flex-col text-xs leading-tight sm:flex">
        <span class="text-muted">Mi carrito</span>
        <span class="price" x-text="total">{{ \App\Support\Money::format($total) }}</span>
    </span>
</a>

<livewire:cart.drawer />
