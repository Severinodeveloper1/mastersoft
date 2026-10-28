@props(['title', 'subtitle' => null, 'kicker' => null])

<div class="border-b border-line bg-surface-alt">
    <div class="container-store py-8 md:py-10">
        <nav class="mb-3 flex items-center gap-2 text-sm text-muted" aria-label="Ruta de navegación">
            <a href="{{ route('home') }}" class="hover:text-primary-dark">Inicio</a>
            <span>/</span>
            <span class="text-ink">{{ $title }}</span>
        </nav>

        @if ($kicker)
            <p class="mb-1 text-xs font-bold tracking-widest text-primary-dark uppercase">{{ $kicker }}</p>
        @endif

        <h1 class="text-3xl font-bold md:text-4xl">{{ $title }}</h1>

        @if ($subtitle)
            <p class="mt-2 max-w-2xl text-muted">{{ $subtitle }}</p>
        @endif
    </div>
</div>
