<section class="container-store py-8 pb-28 md:pb-12">
    <nav class="mb-4 text-sm text-muted" aria-label="Ruta de navegación">
        <a href="{{ route('home') }}" class="hover:text-primary-dark">Inicio</a>
        <span class="px-2">/</span>
        <span class="text-ink">Carrito de compras</span>
    </nav>

    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="font-heading text-3xl font-bold">Carrito de compras</h1>
            <p class="mt-1 text-sm text-muted">{{ $this->count() }} {{ $this->count() === 1 ? 'producto' : 'productos' }}</p>
        </div>
        @if ($this->lines() !== [])
            <button type="button" class="text-sm font-semibold text-accent hover:underline" wire:click="clear">Vaciar carrito</button>
        @endif
    </div>

    @if ($this->lines() === [])
        <div class="rounded-card border border-line bg-surface p-10 text-center shadow-card">
            <x-heroicon-o-shopping-cart class="mx-auto size-12 text-muted" />
            <h2 class="mt-4 font-heading text-2xl font-bold">Tu carrito está vacío</h2>
            <a href="{{ route('store.index') }}" class="mt-5 inline-flex rounded-full bg-primary px-6 py-3 font-semibold text-on-primary hover:bg-primary-hover">Ir a la tienda</a>
        </div>
    @else
        <div class="grid items-start gap-6 lg:grid-cols-3">
            <div class="space-y-4 lg:col-span-2">
                @foreach ($this->lines() as $line)
                    <article wire:key="cart-{{ $line['product']->id }}" class="rounded-card border border-line bg-surface p-4 shadow-card sm:p-5">
                        <div class="flex gap-4">
                            <div class="size-20 shrink-0 overflow-hidden rounded-input bg-surface-alt sm:size-24">
                                @if ($image = $line['product']->primaryImageUrl())
                                    <img src="{{ $image }}" alt="{{ $line['product']->name }}" class="size-full object-contain">
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs text-muted">{{ $line['product']->category?->name }}</p>
                                <h2 class="mt-1 font-semibold">{{ $line['product']->name }}</h2>
                                <p class="price mt-2">{{ \App\Support\Money::format($line['unit_price']) }}</p>
                                @if ($line['product']->stock <= 5)
                                    <p class="mt-1 text-xs text-accent">Últimas {{ $line['product']->stock }} unidades</p>
                                @endif
                                <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                                    <div class="flex items-center rounded-full border border-line bg-surface">
                                        <button type="button" class="px-3 py-1.5" aria-label="Reducir cantidad" wire:click="updateQuantity({{ $line['product']->id }}, {{ $line['quantity'] - 1 }})">−</button>
                                        <span class="min-w-8 text-center text-sm">{{ $line['quantity'] }}</span>
                                        <button type="button" class="px-3 py-1.5" aria-label="Aumentar cantidad" wire:click="updateQuantity({{ $line['product']->id }}, {{ $line['quantity'] + 1 }})">+</button>
                                    </div>
                                    <span class="price">{{ \App\Support\Money::format($line['line_total']) }}</span>
                                    <button type="button" class="text-sm text-muted hover:text-accent" wire:click="remove({{ $line['product']->id }})">Eliminar</button>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
                <a href="{{ route('store.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-dark hover:underline">
                    <x-heroicon-o-arrow-left class="size-4" /> Seguir comprando
                </a>
            </div>

            <aside class="rounded-card border border-line bg-surface p-5 shadow-card lg:sticky lg:top-40">
                <h2 class="font-heading text-xl font-bold">Resumen del pedido</h2>
                <dl class="mt-5 space-y-3 text-sm">
                    <div class="flex justify-between gap-4"><dt>Subtotal (sin IGV)</dt><dd class="price">{{ \App\Support\Money::format($this->subtotal()) }}</dd></div>
                    <div class="flex justify-between gap-4"><dt>IGV (18%)</dt><dd class="price">{{ \App\Support\Money::format($this->tax()) }}</dd></div>
                    <div class="flex justify-between gap-4"><dt>Envío</dt><dd>Por coordinar</dd></div>
                    <div class="flex justify-between gap-4 border-t border-line pt-4 text-base font-bold"><dt>Total</dt><dd class="price">{{ \App\Support\Money::format($this->total()) }}</dd></div>
                </dl>
                <p class="mt-4 text-xs text-muted">Los precios incluyen IGV</p>
                <a href="{{ route('checkout.create') }}" class="mt-5 flex w-full justify-center rounded-full bg-primary px-5 py-3 font-bold text-on-primary hover:bg-primary-hover">Continuar pedido</a>
            </aside>
        </div>

        <div class="fixed inset-x-0 bottom-14 z-20 border-t border-line bg-surface p-3 shadow-lg md:hidden">
            <div class="container-store flex items-center justify-between gap-3">
                <div><span class="block text-xs text-muted">Total</span><span class="price">{{ \App\Support\Money::format($this->total()) }}</span></div>
                <a href="{{ route('checkout.create') }}" class="rounded-full bg-primary px-5 py-3 text-sm font-bold text-on-primary">Continuar pedido</a>
            </div>
        </div>
    @endif
</section>
