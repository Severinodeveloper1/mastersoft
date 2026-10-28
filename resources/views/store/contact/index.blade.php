@php
    $whatsappUrl = $company->whatsappUrl();
    $tiles = array_filter([
        ['icon' => 'heroicon-o-map-pin', 'label' => 'Dirección', 'value' => $company->address, 'href' => null],
        ['icon' => 'heroicon-o-phone', 'label' => 'Teléfono / WhatsApp', 'value' => $company->phone, 'href' => $company->phone ? 'tel:'.preg_replace('/[^+\d]/', '', $company->phone) : null],
        ['icon' => 'heroicon-o-envelope', 'label' => 'Correo electrónico', 'value' => $company->email, 'href' => $company->email ? "mailto:{$company->email}" : null],
        ['icon' => 'heroicon-o-clock', 'label' => 'Horario de atención', 'value' => $company->opening_hours, 'href' => null],
    ], fn (array $tile) => filled($tile['value']));
@endphp

<x-layouts.store title="Contáctanos" description="Canales de contacto de {{ $company->name }}.">
    <x-store.page-header title="Contáctanos" subtitle="¡Estamos aquí para ti! ¿Cómo podemos ayudarte?" />

    <div class="container-store space-y-10 py-10 md:py-14">
        @if ($tiles !== [])
            <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($tiles as $tile)
                    <article class="rounded-card border border-line bg-surface p-5 shadow-card">
                        <span class="mb-4 flex size-11 items-center justify-center rounded-input bg-primary/15">
                            <x-dynamic-component :component="$tile['icon']" class="size-6 text-primary-dark" />
                        </span>
                        <h2 class="text-sm font-semibold text-muted">{{ $tile['label'] }}</h2>
                        @if ($tile['href'])
                            <a href="{{ $tile['href'] }}" class="mt-1 block font-semibold break-words hover:text-primary-dark">{{ $tile['value'] }}</a>
                        @else
                            <p class="mt-1 font-semibold">{{ $tile['value'] }}</p>
                        @endif
                    </article>
                @endforeach
            </section>
        @endif

        <div class="grid gap-8 lg:grid-cols-[minmax(0,3fr)_minmax(0,2fr)]">
            <section class="rounded-card border border-line bg-surface p-6 shadow-card md:p-8">
                <p class="mb-1 text-xs font-bold tracking-widest text-primary-dark uppercase">Vamos a conectarnos</p>
                <h2 class="mb-1 text-2xl font-bold">Envía tu mensaje</h2>
                <p class="mb-6 text-muted">Completa el formulario y te responderemos a la brevedad.</p>
                <livewire:contact.contact-form />
            </section>

            <aside class="space-y-6">
                @if ($mapUrl)
                    <iframe class="h-80 w-full rounded-card border-0" src="{{ $mapUrl }}" title="Mapa de ubicación" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                @elseif ($company->address)
                    <div class="rounded-card border border-line bg-surface-alt p-6">
                        <x-heroicon-o-map-pin class="mb-3 size-8 text-primary-dark" />
                        <h2 class="font-bold">Nuestra ubicación</h2>
                        <p class="mt-1 text-muted">{{ $company->address }}</p>
                    </div>
                @endif

                @if ($whatsappUrl)
                    <div class="rounded-card bg-footer p-6 text-white">
                        <h2 class="text-lg font-bold">¿Prefieres escribirnos directo?</h2>
                        <p class="mt-1 mb-5 text-sm text-white/75">Atención por WhatsApp para consultas y pedidos.</p>
                        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" class="flex items-center justify-center gap-2 rounded-full bg-whatsapp px-5 py-3 font-semibold text-white hover:brightness-95">
                            <x-store.icon-whatsapp class="size-5" /> Escribir por WhatsApp
                        </a>
                    </div>
                @endif
            </aside>
        </div>
    </div>
</x-layouts.store>
