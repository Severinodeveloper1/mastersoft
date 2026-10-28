<x-layouts.store :title="$about->title ?: 'Nosotros'">
    <section class="container-store space-y-12 py-12">
        <div class="grid items-center gap-8 md:grid-cols-2">
            @if ($about->getFirstMediaUrl('image'))
                <img class="w-full rounded-xl object-cover" src="{{ $about->getFirstMediaUrl('image') }}" alt="{{ $about->title }}">
            @endif
            <div>
                @if ($about->kicker)<p class="mb-2 font-semibold text-primary-dark">{{ $about->kicker }}</p>@endif
                @if ($about->title)<h1 class="mb-4 text-4xl font-bold">{{ $about->title }}</h1>@endif
                @if ($about->description)<div class="prose max-w-none">{!! $about->description !!}</div>@endif
                @if ($about->years_of_experience)<p class="mt-6 rounded-xl bg-surface-alt p-5 text-xl font-semibold">{{ $about->years_of_experience }} años de experiencia</p>@endif
                @if ($about->phone)<a class="mt-5 inline-flex items-center gap-2 font-semibold text-primary-dark" href="tel:{{ preg_replace('/[^+\d]/', '', $about->phone) }}"><x-heroicon-o-phone class="size-5" />¿Tienes alguna pregunta? {{ $about->phone }}</a>@endif
            </div>
        </div>
        <div class="grid gap-5 md:grid-cols-3">
            @foreach (['Misión' => $about->mission, 'Visión' => $about->vision, 'Valores' => $about->values] as $heading => $content)
                @if ($content)<article class="rounded-xl border border-gray-200 p-6"><h2 class="mb-3 text-xl font-bold">{{ $heading }}</h2><div class="prose">{!! $content !!}</div></article>@endif
            @endforeach
        </div>
        @if ($brands->isNotEmpty())
            <section aria-label="Marcas">
                <h2 class="mb-5 text-2xl font-bold">Marcas</h2>
                <div class="flex flex-wrap items-center gap-8">
                    @foreach ($brands as $brand)
                        <img class="h-12 max-w-36 object-contain" src="{{ $brand->getFirstMediaUrl('logo') }}" alt="{{ $brand->name }}" loading="lazy">
                    @endforeach
                </div>
            </section>
        @endif
        @if ($company->whatsappUrl())
            <aside class="flex flex-wrap items-center justify-between gap-4 rounded-xl bg-surface-alt p-7">
                <h2 class="text-2xl font-bold">Conversemos</h2>
                <a class="rounded-full bg-whatsapp px-6 py-3 font-semibold text-ink" href="{{ $company->whatsappUrl() }}" target="_blank" rel="noopener">Escríbenos por WhatsApp</a>
            </aside>
        @endif
    </section>
</x-layouts.store>
