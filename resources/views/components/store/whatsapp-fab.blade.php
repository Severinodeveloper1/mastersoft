@php($whatsappUrl = $company->whatsappUrl('Hola Mastersoft, deseo consultar un producto.'))

{{-- Desktop only: on mobile WhatsApp lives in the bottom rail. Hidden while the cart drawer is open. --}}
@if ($whatsappUrl)
    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" aria-label="Escríbenos por WhatsApp"
       x-data="{ hidden: false }" x-show="! hidden" @cart-drawer-toggled.window="hidden = $event.detail.open"
       class="fixed right-6 bottom-6 z-30 hidden size-14 items-center justify-center rounded-full bg-whatsapp text-white shadow-float transition hover:scale-105 md:flex">
        <x-store.icon-whatsapp class="size-7" />
    </a>
@endif
