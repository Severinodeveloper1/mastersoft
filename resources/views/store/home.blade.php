<x-layouts.store :title="$title" :description="$description">
    <div class="container-store py-6 md:py-10">
        @if ($heroBanners->isNotEmpty() || $sideBanner)
            <section class="grid gap-4 lg:grid-cols-[minmax(0,2fr)_minmax(260px,1fr)]" aria-label="Promociones destacadas">
                @if ($heroBanners->isNotEmpty())
                    <div class="relative min-h-64 overflow-hidden rounded-card bg-surface-alt md:min-h-[390px]" x-data="{ active: 0, total: {{ $heroBanners->count() }} }" x-init="if (total > 1) setInterval(() => active = (active + 1) % total, 5500)">
                        @foreach ($heroBanners as $index => $banner)
                            @php($image = $banner->getFirstMediaUrl('image'))
                            <a href="{{ $banner->link_url ?: route('store.index') }}" x-show="active === {{ $index }}" x-transition.opacity class="absolute inset-0">
                                @if ($image)<img src="{{ $image }}" alt="{{ $banner->title ?: 'Productos destacados de Mastersoft' }}" class="size-full object-cover">@endif
                                @if ($banner->title)<span class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 p-6 pt-16 text-xl font-bold text-white md:text-3xl">{{ $banner->title }}</span>@endif
                            </a>
                        @endforeach
                        @if ($heroBanners->count() > 1)
                            <div class="absolute bottom-3 right-4 flex gap-2">
                                @foreach ($heroBanners as $index => $banner)
                                    <button type="button" @click="active={{ $index }}" :aria-current="active === {{ $index }}" aria-label="Ver banner {{ $index + 1 }}" class="size-2.5 rounded-full bg-white/70" :class="active === {{ $index }} ? 'w-6 bg-primary' : ''"></button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
                @if ($sideBanner)
                    <a href="{{ $sideBanner->link_url ?: route('store.index') }}" class="relative min-h-48 overflow-hidden rounded-card bg-surface-alt">
                        @if ($image = $sideBanner->getFirstMediaUrl('image'))<img src="{{ $image }}" alt="{{ $sideBanner->title ?: 'Selección Mastersoft' }}" class="absolute inset-0 size-full object-cover">@endif
                        @if ($sideBanner->title)<span class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 p-5 pt-12 font-bold text-white">{{ $sideBanner->title }}</span>@endif
                    </a>
                @endif
            </section>
        @endif

        <section class="my-8 grid grid-cols-2 gap-4 rounded-card border border-line bg-surface p-5 md:my-10 md:grid-cols-4" aria-label="Servicios">
            @foreach ([['Pedidos por WhatsApp', 'Rápido y sin registro', 'chat-bubble-left-right'], ['Envíos a todo el Perú', 'Por agencia o a domicilio', 'truck'], ['Asesoría técnica', 'Te ayudamos a elegir', 'wrench-screwdriver'], ['Recojo en tienda', 'Coordina por WhatsApp', 'building-storefront']] as [$heading, $copy, $icon])
                <div class="flex items-start gap-3"><x-dynamic-component :component="'heroicon-o-'.$icon" class="mt-0.5 size-5 shrink-0 text-primary-dark"/><div><h2 class="text-sm font-bold">{{ $heading }}</h2><p class="mt-1 text-xs text-muted">{{ $copy }}</p></div></div>
            @endforeach
        </section>

        @if ($categories->isNotEmpty())
            <section class="mb-10">
                <div class="mb-4 flex items-end justify-between"><div><p class="text-xs font-bold uppercase tracking-widest text-primary-dark">Encuentra lo que necesitas</p><h2 class="mt-1 text-2xl font-bold">Compra por categoría</h2></div><a href="{{ route('store.index') }}" class="text-sm font-semibold text-primary-dark hover:underline">Ver todas</a></div>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
                    @foreach ($categories as $category)
                        <a href="{{ route('store.index', ['categoria' => $category->slug]) }}" class="rounded-card border border-line bg-surface p-4 transition hover:border-primary hover:shadow-card-hover"><span class="mb-3 flex size-10 items-center justify-center rounded-full bg-primary/15"><x-heroicon-o-squares-2x2 class="size-5 text-primary-dark" /></span><span class="block font-semibold">{{ $category->name }}</span><span class="mt-1 block text-xs text-muted">{{ $category->visible_products_count }} productos</span></a>
                    @endforeach
                </div>
            </section>
        @endif

        @if ($promotions->isNotEmpty())
            <section class="mb-10"><h2 class="mb-4 text-2xl font-bold">Nuestras promociones</h2><div class="grid gap-4 md:grid-cols-2">
                @foreach ($promotions as $promotion)
                    @php($url = $promotion->product ? route('products.show', $promotion->product) : ($promotion->link_url ?: route('store.index')))
                    <a href="{{ $url }}" class="group relative flex min-h-40 items-end overflow-hidden rounded-card bg-surface-alt">@if ($image = $promotion->getFirstMediaUrl('image'))<img src="{{ $image }}" alt="{{ $promotion->title }}" class="absolute inset-0 size-full object-cover transition group-hover:scale-[1.02]">@endif<span class="relative w-full bg-gradient-to-t from-black/75 p-5 pt-14 text-lg font-bold text-white">{{ $promotion->title }}</span></a>
                @endforeach
            </div></section>
        @endif

        @if ($featuredProducts->isNotEmpty() || $latestProducts->isNotEmpty())
            <section class="mb-10" x-data="{ tab: '{{ $featuredProducts->isNotEmpty() ? 'featured' : 'latest' }}' }">
                <div class="mb-5 flex flex-wrap items-end justify-between gap-4"><div><p class="text-xs font-bold uppercase tracking-widest text-primary-dark">Selección para ti</p><h2 class="mt-1 text-2xl font-bold">Nuestros Productos</h2></div><div class="flex gap-2" role="tablist" aria-label="Productos">@if ($featuredProducts->isNotEmpty())<button type="button" @click="tab='featured'" :aria-selected="tab==='featured'" class="rounded-full border px-4 py-2 text-sm font-semibold" :class="tab==='featured' ? 'border-primary bg-primary text-on-primary' : 'border-line'">Destacados</button>@endif @if ($latestProducts->isNotEmpty())<button type="button" @click="tab='latest'" :aria-selected="tab==='latest'" class="rounded-full border px-4 py-2 text-sm font-semibold" :class="tab==='latest' ? 'border-primary bg-primary text-on-primary' : 'border-line'">Recién llegados</button>@endif</div></div>
                @foreach (['featured' => $featuredProducts, 'latest' => $latestProducts] as $key => $products)
                    @if ($products->isNotEmpty())<div x-show="tab==='{{ $key }}'" class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">@foreach ($products as $product)<x-store.product-card :product="$product" />@endforeach</div>@endif
                @endforeach
                <div class="mt-6 text-center"><a href="{{ route('store.index') }}" class="inline-flex rounded-full bg-primary px-6 py-3 text-sm font-bold text-on-primary hover:bg-primary-hover">Ver toda la tienda</a></div>
            </section>
        @endif

        @if ($bestSellers->isNotEmpty())
            <section class="mb-10"><div class="mb-4"><p class="text-xs font-bold uppercase tracking-widest text-primary-dark">Lo que más eligen</p><h2 class="mt-1 text-2xl font-bold">Productos más vendidos</h2></div><div class="flex gap-3 overflow-x-auto pb-3 md:grid md:grid-cols-3 md:overflow-visible">
                @foreach ($bestSellers as $product)<a href="{{ route('products.show', $product) }}" class="flex min-w-64 items-center gap-3 md:min-w-0 rounded-card border border-line bg-surface p-3 hover:border-primary">@if ($image = $product->getFirstMediaUrl('images'))<img src="{{ $image }}" alt="{{ $product->name }}" class="size-16 rounded-input bg-surface-alt object-contain">@else<span class="flex size-16 items-center justify-center rounded-input bg-surface-alt"><x-heroicon-o-photo class="size-6 text-muted" /></span>@endif<div class="min-w-0"><p class="line-clamp-2 text-sm font-semibold">{{ $product->name }}</p><p class="price mt-1 text-primary-dark">{{ \App\Support\Money::format($product->price) }}</p></div></a>@endforeach
            </div></section>
        @endif

        @if ($company->whatsappUrl())
            <section class="mb-6 flex flex-col items-start justify-between gap-4 rounded-card bg-ink p-6 text-white sm:flex-row sm:items-center md:p-8"><div><h2 class="text-xl font-bold md:text-2xl">¿No encuentras lo que buscas?</h2><p class="mt-1 text-white/75">Escríbenos y te ayudamos</p></div><a href="{{ $company->whatsappUrl('Hola Mastersoft, necesito ayuda para encontrar un producto.') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-full bg-whatsapp px-5 py-3 font-bold text-white"><x-store.icon-whatsapp class="size-5" /> Escribir por WhatsApp</a></section>
        @endif
    </div>
</x-layouts.store>
