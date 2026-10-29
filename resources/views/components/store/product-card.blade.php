@props(['product'])

@php
    use App\Support\Money;

    $image = $product->primaryImageUrl();
    $url = route('products.show', $product);
@endphp

<article {{ $attributes->class('group flex flex-col rounded-card border border-line bg-surface p-3 shadow-card transition hover:-translate-y-0.5 hover:border-primary hover:shadow-card-hover') }}>
    <a href="{{ $url }}" class="relative mb-3 flex aspect-square items-center justify-center overflow-hidden rounded-input bg-surface-alt">
        @if ($image)
            <img src="{{ $image }}" alt="{{ $product->name }}" loading="lazy" class="size-full object-contain p-3">
        @else
            <x-heroicon-o-photo class="size-12 text-line" />
        @endif

        @if ($product->isOnSale())
            <span class="absolute top-2 left-2 rounded-full bg-accent px-2.5 py-0.5 text-[11px] font-bold text-white">Oferta -{{ $product->discountPercentage() }}%</span>
        @endif
    </a>

    @if ($product->category)
        <p class="mb-1 text-[11px] tracking-wider text-muted uppercase">{{ $product->category->name }}</p>
    @endif

    <h3 class="mb-2 line-clamp-2 min-h-10 text-sm font-semibold">
        <a href="{{ $url }}" class="hover:text-primary-dark">{{ $product->name }}</a>
    </h3>

    <div class="mt-auto mb-3 flex items-baseline gap-2">
        <span @class(['price text-lg', 'text-accent' => $product->isOnSale()])>{{ Money::format($product->price) }}</span>
        @if ($product->isOnSale())
            <span class="price text-xs font-normal text-muted line-through">{{ Money::format($product->regular_price) }}</span>
        @endif
    </div>

    <x-store.add-to-cart-button :product="$product" />
</article>
