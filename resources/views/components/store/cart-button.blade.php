{{-- Header cart access. Task T5 replaces the static count and total with the live cart. --}}
@props(['count' => 0, 'total' => 0])

<a href="{{ route('cart.show') }}" class="relative flex items-center gap-2 rounded-full bg-surface-alt px-3 py-1.5 hover:bg-line" aria-label="Carrito de compras">
    <span class="relative">
        <x-heroicon-o-shopping-cart class="size-6" />
        <span class="absolute -top-1.5 -right-1.5 flex size-4 items-center justify-center rounded-full bg-accent text-[10px] font-bold text-white">{{ $count }}</span>
    </span>
    <span class="hidden flex-col text-xs leading-tight sm:flex">
        <span class="text-muted">Mi carrito</span>
        <span class="price">{{ \App\Support\Money::format($total) }}</span>
    </span>
</a>
