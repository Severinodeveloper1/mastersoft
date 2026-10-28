<div
    x-data="{ open: false }"
    x-init="if (@js(session('cart_drawer_open', false))) { open = true; $dispatch('cart-drawer-toggled', { open: true }) }"
    @open-cart-drawer.window="open = true; $dispatch('cart-drawer-toggled', { open: true })"
    @keydown.escape.window="open = false; $dispatch('cart-drawer-toggled', { open: false })"
>
    <div x-cloak x-show="open" class="fixed inset-0 z-50">
        <button
            type="button"
            class="absolute inset-0 bg-black/40"
            aria-label="Cerrar carrito"
            @click="open = false; $dispatch('cart-drawer-toggled', { open: false })"
        ></button>

        <aside
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="absolute inset-y-0 right-0 flex w-full max-w-md flex-col bg-surface shadow-xl"
            role="dialog"
            aria-modal="true"
            aria-label="Tu carrito"
        >
            <header class="flex items-center justify-between border-b border-line px-5 py-4">
                <h2 class="font-heading text-xl font-bold">Tu carrito ({{ $this->count() }})</h2>
                <button
                    type="button"
                    class="rounded-full p-2 hover:bg-surface-alt"
                    aria-label="Cerrar carrito"
                    @click="open = false; $dispatch('cart-drawer-toggled', { open: false })"
                >
                    <x-heroicon-o-x-mark class="size-5" />
                </button>
            </header>

            <div class="flex-1 overflow-y-auto px-5 py-4">
                @if (session('status') === 'Producto agregado al carrito')
                    <p class="mb-3 rounded-input bg-primary/15 px-4 py-3 text-sm font-semibold" role="status">
                        Producto agregado al carrito.
                        <a href="{{ route('cart.show') }}" class="ml-1 underline">Ver carrito</a>
                    </p>
                @endif
                @forelse ($this->lines() as $line)
                    <article wire:key="drawer-{{ $line['product']->id }}" class="flex gap-3 border-b border-line py-4">
                        <div class="size-16 shrink-0 overflow-hidden rounded-input bg-surface-alt">
                            @if ($image = $line['product']->getFirstMediaUrl('images'))
                                <img src="{{ $image }}" alt="{{ $line['product']->name }}" class="size-full object-contain">
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="line-clamp-2 text-sm font-semibold">{{ $line['product']->name }}</h3>
                            <p class="price mt-1 text-sm">{{ \App\Support\Money::format($line['unit_price']) }}</p>
                            <div class="mt-2 flex items-center justify-between">
                                <div class="flex items-center rounded-full border border-line">
                                    <button type="button" class="px-2 py-1" aria-label="Reducir cantidad" wire:click="updateQuantity({{ $line['product']->id }}, {{ $line['quantity'] - 1 }})">−</button>
                                    <span class="min-w-7 text-center text-sm">{{ $line['quantity'] }}</span>
                                    <button type="button" class="px-2 py-1" aria-label="Aumentar cantidad" wire:click="updateQuantity({{ $line['product']->id }}, {{ $line['quantity'] + 1 }})">+</button>
                                </div>
                                <button type="button" class="text-xs text-accent hover:underline" wire:click="remove({{ $line['product']->id }})">Eliminar</button>
                            </div>
                        </div>
                    </article>
                @empty
                    <p class="py-10 text-center text-sm text-muted">Tu carrito está vacío.</p>
                @endforelse
            </div>

            @if ($this->count() > 0)
                <footer class="border-t border-line p-5">
                    <div class="mb-4 flex items-center justify-between">
                        <span class="text-sm text-muted">Subtotal</span>
                        <span class="price">{{ \App\Support\Money::format($this->total()) }}</span>
                    </div>
                    <a href="{{ route('cart.show') }}" class="mb-2 flex w-full justify-center rounded-full border border-line px-4 py-3 text-sm font-semibold hover:bg-surface-alt">Ver carrito</a>
                    <a href="{{ route('checkout.create') }}" class="flex w-full justify-center rounded-full bg-primary px-4 py-3 text-sm font-bold text-on-primary hover:bg-primary-hover">Continuar pedido</a>
                </footer>
            @endif
        </aside>
    </div>
</div>
