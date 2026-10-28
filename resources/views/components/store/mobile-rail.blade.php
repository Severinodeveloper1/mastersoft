@php($whatsappUrl = $company->whatsappUrl())

{{-- Fixed bottom action rail on mobile; the cart page and order form add their own sticky bar above it. --}}
<nav class="fixed inset-x-0 bottom-0 z-30 grid grid-cols-4 border-t border-line bg-surface text-xs md:hidden" aria-label="Acciones rápidas">
    <button type="button" class="flex flex-col items-center gap-1 py-2" x-data @click="$dispatch('open-store-menu')">
        <x-heroicon-o-squares-2x2 class="size-6" /> Categorías
    </button>
    <a href="{{ route('store.index') }}" class="flex flex-col items-center gap-1 py-2">
        <x-heroicon-o-magnifying-glass class="size-6" /> Buscar
    </a>
    <a href="{{ route('cart.show') }}" class="flex flex-col items-center gap-1 py-2">
        <x-heroicon-o-shopping-cart class="size-6" /> Carrito
    </a>
    @if ($whatsappUrl)
        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" class="flex flex-col items-center gap-1 py-2 font-semibold text-[#128c4a]">
            <x-store.icon-whatsapp class="size-6" /> WhatsApp
        </a>
    @else
        <a href="{{ route('contact') }}" class="flex flex-col items-center gap-1 py-2">
            <x-heroicon-o-chat-bubble-left-right class="size-6" /> Contacto
        </a>
    @endif
</nav>
