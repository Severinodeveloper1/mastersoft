<x-layouts.store :title="$title" :description="$description">
    <section class="container-store py-8 md:py-12">
        <nav class="mb-4 text-sm text-muted" aria-label="Ruta de navegación"><a href="{{ route('home') }}" class="hover:text-primary-dark">Inicio</a><span class="mx-2">/</span><span class="text-ink">Tienda</span></nav>
        <h1 class="mb-6 text-3xl font-bold">Tienda</h1>
        @livewire('catalog.product-browser')
    </section>
</x-layouts.store>
