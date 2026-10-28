@php
    $image = $about->getFirstMediaUrl('image');
    $pillars = array_filter([
        ['heading' => 'Misión', 'icon' => 'heroicon-o-flag', 'content' => $about->mission],
        ['heading' => 'Visión', 'icon' => 'heroicon-o-eye', 'content' => $about->vision],
        ['heading' => 'Valores', 'icon' => 'heroicon-o-shield-check', 'content' => $about->values],
    ], fn (array $pillar) => filled($pillar['content']));
    $hasIntro = filled($about->title) || filled($about->description) || $image;
    $hasContent = $hasIntro || $pillars !== [] || $brands->isNotEmpty();
@endphp

<x-layouts.store title="Nosotros" description="Conoce a {{ $company->name }}.">
    <x-store.page-header title="Nosotros" />

    <div class="container-store space-y-14 py-10 md:py-14">
        @if ($hasIntro)
            <section @class(['grid items-center gap-10', 'lg:grid-cols-2' => $image])>
                @if ($image)
                    <img src="{{ $image }}" alt="{{ $about->title ?? $company->name }}" class="aspect-[4/3] w-full rounded-card object-cover">
                @endif

                <div>
                    @if ($about->kicker)
                        <p class="mb-2 text-xs font-bold tracking-widest text-primary-dark uppercase">{{ $about->kicker }}</p>
                    @endif
                    @if ($about->title)
                        <h2 class="mb-4 text-3xl font-bold">{{ $about->title }}</h2>
                    @endif
                    @if ($about->description)
                        <div class="space-y-4 leading-relaxed text-muted">{!! $about->description !!}</div>
                    @endif

                    @if ($about->years_of_experience || $about->phone)
                        <div class="mt-8 grid gap-4 sm:grid-cols-2">
                            @if ($about->years_of_experience)
                                <div class="rounded-card bg-surface-alt p-5">
                                    <p class="price text-3xl text-primary-dark">{{ $about->years_of_experience }}</p>
                                    <p class="text-sm text-muted">años de experiencia</p>
                                </div>
                            @endif
                            @if ($about->phone)
                                <a href="tel:{{ preg_replace('/[^+\d]/', '', $about->phone) }}" class="flex items-center gap-3 rounded-card border border-line p-5 hover:border-primary">
                                    <x-heroicon-o-phone class="size-8 shrink-0 text-primary-dark" />
                                    <span>
                                        <span class="block text-sm text-muted">¿Tienes alguna pregunta?</span>
                                        <span class="font-semibold">{{ $about->phone }}</span>
                                    </span>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </section>
        @endif

        @if ($pillars !== [])
            <section class="grid gap-6 md:grid-cols-3">
                @foreach ($pillars as $pillar)
                    <article class="rounded-card border border-line bg-surface p-6 shadow-card">
                        <span class="mb-4 flex size-12 items-center justify-center rounded-full bg-primary/15">
                            <x-dynamic-component :component="$pillar['icon']" class="size-6 text-primary-dark" />
                        </span>
                        <h2 class="mb-3 text-xl font-bold">{{ $pillar['heading'] }}</h2>
                        <div class="space-y-2 text-muted">{!! $pillar['content'] !!}</div>
                    </article>
                @endforeach
            </section>
        @endif

        @if ($brands->isNotEmpty())
            <section>
                <h2 class="mb-6 text-2xl font-bold">Marcas con las que trabajamos</h2>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                    @foreach ($brands as $brand)
                        <div class="flex h-24 items-center justify-center rounded-card border border-line bg-surface p-4">
                            <img src="{{ $brand->getFirstMediaUrl('logo') }}" alt="{{ $brand->name }}" class="max-h-12 w-auto object-contain grayscale transition hover:grayscale-0">
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @unless ($hasContent)
            <section class="rounded-card border border-dashed border-line py-16 text-center">
                <x-heroicon-o-building-storefront class="mx-auto mb-4 size-12 text-muted" />
                <p class="text-lg font-semibold">Estamos preparando esta sección.</p>
                <p class="mt-1 text-muted">Mientras tanto, puedes escribirnos y te contamos más sobre nosotros.</p>
                <a href="{{ route('contact') }}" class="mt-6 inline-flex rounded-full bg-primary px-6 py-2.5 font-semibold text-on-primary hover:bg-primary-hover">Contáctanos</a>
            </section>
        @endunless

        @if ($company->whatsappUrl())
            <section class="flex flex-col items-start gap-4 rounded-card bg-footer p-8 text-white md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-2xl font-bold">¿No encuentras lo que buscas?</h2>
                    <p class="text-white/75">Escríbenos y te ayudamos.</p>
                </div>
                <a href="{{ $company->whatsappUrl() }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-full bg-whatsapp px-6 py-3 font-semibold text-white hover:brightness-95">
                    <x-store.icon-whatsapp class="size-5" /> Escribir por WhatsApp
                </a>
            </section>
        @endif
    </div>
</x-layouts.store>
