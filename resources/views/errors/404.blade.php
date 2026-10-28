<x-layouts.store title="Página no encontrada">
    <section class="container-store flex min-h-[55vh] flex-col items-center justify-center py-16 text-center">
        <x-heroicon-o-magnifying-glass class="mb-5 size-12 text-primary-dark" />
        <h1 class="text-4xl font-bold">Página no encontrada</h1>
        <p class="mt-3 max-w-xl text-muted">La página que buscas no está disponible. Puedes buscar un producto o volver a la tienda.</p>
        <form action="{{ route('store.index') }}" method="GET" class="mt-7 flex w-full max-w-lg gap-2">
            <input class="min-w-0 flex-1 rounded-lg border border-gray-300 p-3" type="search" name="buscar" placeholder="Buscar producto" aria-label="Buscar producto">
            <button class="rounded-full bg-primary px-5 py-3 font-semibold text-on-primary" type="submit">Buscar</button>
        </form>
        <div class="mt-6 flex flex-wrap justify-center gap-3">
            <a class="rounded-full border border-gray-300 px-5 py-3 font-semibold" href="{{ route('home') }}">Volver al inicio</a>
            <a class="rounded-full bg-primary px-5 py-3 font-semibold text-on-primary" href="{{ route('store.index') }}">Ir a la tienda</a>
        </div>
    </section>
</x-layouts.store>
