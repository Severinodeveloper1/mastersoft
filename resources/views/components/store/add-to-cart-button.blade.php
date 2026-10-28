{{-- Plain form fallback; task T5 wires it to the live cart (drawer + toast). --}}
@props(['product', 'quantity' => 1, 'label' => 'Agregar al carrito'])

<form method="POST" action="{{ route('cart.add') }}" {{ $attributes->class('w-full') }}>
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">
    <input type="hidden" name="quantity" value="{{ $quantity }}">
    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-full bg-primary px-4 py-2 text-sm font-bold text-on-primary transition hover:bg-primary-hover active:scale-[0.98]">
        <x-heroicon-o-shopping-cart class="size-4" /> {{ $label }}
    </button>
</form>
