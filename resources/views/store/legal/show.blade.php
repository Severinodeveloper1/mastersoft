<x-layouts.store :title="$page->title">
    <article class="container-store max-w-4xl py-12">
        <header class="mb-8 border-b border-gray-200 pb-6">
            <h1 class="text-3xl font-bold">{{ $page->title }}</h1>
            <p class="mt-3 text-sm text-muted">Última actualización: {{ $page->updated_at->locale('es')->translatedFormat('d \d\e F \d\e Y') }}</p>
        </header>
        <nav class="mb-8 flex flex-wrap gap-5" aria-label="Documentos legales">
            <a href="{{ route('legal.privacy') }}" class="font-semibold {{ $page->slug === \App\Models\LegalPage::PRIVACY ? 'text-primary-dark' : 'text-muted' }}">Políticas de privacidad</a>
            <a href="{{ route('legal.terms') }}" class="font-semibold {{ $page->slug === \App\Models\LegalPage::TERMS ? 'text-primary-dark' : 'text-muted' }}">Términos y condiciones</a>
        </nav>
        @if (trim(strip_tags($page->content ?? '')) === '')
            <p class="rounded-xl bg-surface-alt p-6">Contenido en preparación.</p>
        @else
            <div class="prose max-w-none">{!! $page->content !!}</div>
        @endif
    </article>
</x-layouts.store>
