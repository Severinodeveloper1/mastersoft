{{-- Brand-styled Livewire pagination: $products->links('components.store.pagination') --}}
@if ($paginator->hasPages())
    <nav class="flex items-center justify-between gap-4 text-sm" aria-label="Paginación">
        <p class="text-muted">
            Mostrando {{ $paginator->firstItem() }} - {{ $paginator->lastItem() }} de {{ $paginator->total() }} productos
        </p>

        <div class="flex items-center gap-1">
            <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" @disabled($paginator->onFirstPage())
                    class="flex size-9 items-center justify-center rounded-full border border-line hover:border-primary disabled:opacity-40" aria-label="Página anterior">
                <x-heroicon-o-chevron-left class="size-4" />
            </button>

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 text-muted">{{ $element }}</span>
                @else
                    @foreach ($element as $page => $url)
                        <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                @if ($page === $paginator->currentPage()) aria-current="page" @endif
                                @class([
                                    'flex size-9 items-center justify-center rounded-full font-semibold',
                                    'bg-primary text-on-primary' => $page === $paginator->currentPage(),
                                    'hover:bg-surface-alt' => $page !== $paginator->currentPage(),
                                ])>{{ $page }}</button>
                    @endforeach
                @endif
            @endforeach

            <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" @disabled(! $paginator->hasMorePages())
                    class="flex size-9 items-center justify-center rounded-full border border-line hover:border-primary disabled:opacity-40" aria-label="Página siguiente">
                <x-heroicon-o-chevron-right class="size-4" />
            </button>
        </div>
    </nav>
@endif
