<div>
    @php
        $searchTerm = is_string($buscar) ? trim($buscar) : '';
        $selectedCategory = is_string($categoria) ? $categoria : '';
        $selectedBrands = is_array($marcas) ? array_values(array_filter($marcas, 'is_string')) : [];
        $minimumPrice = is_scalar($precio_min) ? (string) $precio_min : '';
        $maximumPrice = is_scalar($precio_max) ? (string) $precio_max : '';
    @endphp
    <div class="grid gap-6 lg:grid-cols-[260px_minmax(0,1fr)]">
        <aside class="hidden lg:block"><div class="sticky top-40 rounded-card border border-line bg-surface p-5"><h2 class="mb-4 font-bold">Filtros</h2>@include('livewire.catalog.filters')</div></aside>
        <div class="min-w-0">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <p class="text-sm text-muted"><strong class="text-ink">{{ $products->total() }} productos</strong></p>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" wire:click="$set('filtersOpen', true)" class="inline-flex items-center gap-2 rounded-input border border-line px-3 py-2 text-sm lg:hidden"><x-heroicon-o-funnel class="size-4" /> Filtrar</button>
                    <label class="flex items-center gap-2 text-sm"><span class="sr-only">Ordenar productos</span><select wire:model.live="orden" class="rounded-input border border-line bg-surface px-3 py-2"><option value="relevancia">Relevancia</option><option value="menor-precio">Menor precio</option><option value="mayor-precio">Mayor precio</option><option value="nombre">Nombre</option></select></label>
                    <div class="hidden rounded-input border border-line p-1 sm:flex" aria-label="Vista de productos"><button type="button" wire:click="$set('vista', 'grid')" aria-label="Vista cuadrícula" @class(['rounded p-1.5', 'bg-primary/20 text-primary-dark' => $vista !== 'lista'])><x-heroicon-o-squares-2x2 class="size-5" /></button><button type="button" wire:click="$set('vista', 'lista')" aria-label="Vista lista" @class(['rounded p-1.5', 'bg-primary/20 text-primary-dark' => $vista === 'lista'])><x-heroicon-o-bars-3-bottom-left class="size-5" /></button></div>
                </div>
            </div>
            @php($activeBrands = collect($selectedBrands)->filter(fn ($slug) => $brands->contains('slug', $slug)))
            @if ($searchTerm !== '' || ($selectedCategory && $categories->contains('slug', $selectedCategory)) || $activeBrands->isNotEmpty() || $minimumPrice !== '' || $maximumPrice !== '')
                <div class="mb-4 flex flex-wrap items-center gap-2" aria-label="Filtros activos">
                    @if ($searchTerm !== '')<button type="button" wire:click="$set('buscar', '')" class="rounded-full bg-surface-alt px-3 py-1 text-xs">{{ $searchTerm }} <span aria-hidden="true">×</span></button>@endif
                    @if ($category = $categories->firstWhere('slug', $selectedCategory))<button type="button" wire:click="$set('categoria', '')" class="rounded-full bg-surface-alt px-3 py-1 text-xs">{{ $category->name }} ×</button>@endif
                    @foreach ($activeBrands as $slug)<button type="button" wire:click="$set('marcas', {{ Illuminate\Support\Js::from(array_values(array_diff($selectedBrands, [$slug]))) }})" class="rounded-full bg-surface-alt px-3 py-1 text-xs">{{ $brands->firstWhere('slug', $slug)?->name }} ×</button>@endforeach
                    @if ($minimumPrice !== '' || $maximumPrice !== '')<button type="button" wire:click="$set('precio_min', ''); $set('precio_max', '')" class="rounded-full bg-surface-alt px-3 py-1 text-xs">Rango de precio ×</button>@endif
                    <button type="button" wire:click="clearFilters" class="text-xs font-semibold text-primary-dark underline">Limpiar filtros</button>
                </div>
            @endif
            @if ($products->isNotEmpty())
                @if ($vista === 'lista')
                    <div class="space-y-3">@foreach ($products as $product)<article class="flex items-center gap-4 rounded-card border border-line p-3"><a href="{{ route('products.show', $product) }}" class="flex size-24 shrink-0 items-center justify-center rounded-input bg-surface-alt">@if ($image = $product->primaryImageUrl())<img src="{{ $image }}" alt="{{ $product->name }}" class="size-full object-contain">@else<x-heroicon-o-photo class="size-8 text-muted"/>@endif</a><div class="min-w-0 flex-1"><p class="text-xs text-muted">{{ $product->category?->name }}</p><h2 class="line-clamp-2 font-semibold"><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></h2><p class="price mt-2 text-primary-dark">{{ \App\Support\Money::format($product->price) }}</p></div><div class="hidden w-44 sm:block"><x-store.add-to-cart-button :product="$product" /></div></article>@endforeach</div>
                @else
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-4">@foreach ($products as $product)<x-store.product-card :product="$product" />@endforeach</div>
                @endif
                <div class="mt-6 hidden md:block">{{ $products->links('components.store.pagination') }}</div>
                @if ($products->hasMorePages())<div class="mt-6 text-center md:hidden"><button type="button" wire:click="loadMore" wire:loading.attr="disabled" class="rounded-full border border-primary-dark px-6 py-3 text-sm font-bold text-primary-dark">Cargar más</button></div>@endif
            @else
                <div class="rounded-card border border-dashed border-line bg-surface-alt px-6 py-12 text-center"><span class="mx-auto mb-4 flex size-14 items-center justify-center rounded-full bg-surface"><x-heroicon-o-magnifying-glass class="size-7 text-muted" /></span><h2 class="text-xl font-bold">No encontramos productos para
                    @if ($searchTerm !== '')
                        “{{ $searchTerm }}”
                    @else
                        tu búsqueda
                    @endif
                </h2><p class="mx-auto mt-2 max-w-lg text-sm text-muted">Prueba con otra palabra o explora estas categorías.</p><div class="mt-5 flex flex-wrap justify-center gap-2">@foreach ($categories->take(5) as $category)<a href="{{ route('store.index', ['categoria' => $category->slug]) }}" class="rounded-full border border-line bg-surface px-4 py-2 text-sm">{{ $category->name }}</a>@endforeach</div>@if ($company->whatsappUrl())<a href="{{ $company->whatsappUrl('Hola Mastersoft, necesito ayuda para encontrar un producto.') }}" class="mt-6 inline-flex items-center gap-2 rounded-full bg-whatsapp px-5 py-3 font-semibold text-white" target="_blank" rel="noopener"><x-store.icon-whatsapp class="size-5"/> Consultar por WhatsApp</a>@endif</div>
            @endif
        </div>
    </div>
    <div x-cloak x-show="$wire.filtersOpen" x-transition class="fixed inset-0 z-50 lg:hidden" role="dialog" aria-modal="true" aria-label="Filtros de productos"><button type="button" class="absolute inset-0 size-full bg-black/40" wire:click="$set('filtersOpen', false)" aria-label="Cerrar filtros"></button><div class="absolute inset-x-0 bottom-0 max-h-[85vh] overflow-y-auto rounded-t-2xl bg-surface p-5 pb-8"><div class="mb-4 flex items-center justify-between"><h2 class="text-lg font-bold">Filtros</h2><button type="button" wire:click="$set('filtersOpen', false)" aria-label="Cerrar filtros"><x-heroicon-o-x-mark class="size-6" /></button></div>@include('livewire.catalog.filters')<button type="button" wire:click="$set('filtersOpen', false)" class="mt-5 w-full rounded-full bg-primary px-5 py-3 font-bold text-on-primary">Aplicar filtros</button></div></div>
</div>
