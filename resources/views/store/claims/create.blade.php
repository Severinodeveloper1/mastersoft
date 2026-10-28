<x-layouts.store title="Libro de Reclamaciones Virtual">
    <section class="container-store py-10">
        <header class="mb-8 flex items-start gap-4 rounded-xl bg-surface-alt p-6">
            <x-heroicon-o-book-open class="size-10 shrink-0 text-primary-dark" />
            <div><h1 class="text-3xl font-bold">Libro de Reclamaciones Virtual</h1>
                @if ($company->legal_name)<p class="mt-2">{{ $company->legal_name }}</p>@endif
                @if ($company->ruc)<p>RUC {{ $company->ruc }}</p>@endif
                <p class="mt-1 text-muted">{{ now()->locale('es')->translatedFormat('d \d\e F \d\e Y') }}</p>
            </div>
        </header>
        <livewire:claims.claim-form />
    </section>
</x-layouts.store>
