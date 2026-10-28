@php
    $socials = array_filter([
        'youtube' => $company->youtube_url,
        'facebook' => $company->facebook_url,
        'linkedin' => $company->linkedin_url,
        'instagram' => $company->instagram_url,
    ]);
@endphp

<footer class="bg-footer pt-12 pb-6 text-sm text-white/75">
    <div class="container-store grid gap-10 pb-10 md:grid-cols-2 lg:grid-cols-4">
        <div class="space-y-3">
            <p class="font-heading text-lg font-bold text-white">{{ $company->name }}</p>
            @if ($company->description)
                <p class="leading-relaxed">{{ $company->description }}</p>
            @endif
        </div>

        <div>
            <p class="mb-4 font-heading font-bold tracking-wide text-white uppercase">Enlaces</p>
            <ul class="space-y-2">
                <li><a href="{{ route('home') }}" class="hover:text-primary">Inicio</a></li>
                <li><a href="{{ route('store.index') }}" class="hover:text-primary">Tienda</a></li>
                <li><a href="{{ route('about') }}" class="hover:text-primary">Nosotros</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-primary">Contáctanos</a></li>
                <li><a href="{{ route('legal.privacy') }}" class="hover:text-primary">Políticas de privacidad</a></li>
                <li><a href="{{ route('legal.terms') }}" class="hover:text-primary">Términos y condiciones</a></li>
            </ul>
        </div>

        <div>
            <p class="mb-4 font-heading font-bold tracking-wide text-white uppercase">Contacto</p>
            <ul class="space-y-3">
                @if ($company->address)
                    <li class="flex gap-2"><x-heroicon-o-map-pin class="size-5 shrink-0 text-primary" /> {{ $company->address }}</li>
                @endif
                @if ($company->phone)
                    <li class="flex gap-2"><x-heroicon-o-phone class="size-5 shrink-0 text-primary" /> {{ $company->phone }}</li>
                @endif
                @if ($company->email)
                    <li class="flex gap-2"><x-heroicon-o-envelope class="size-5 shrink-0 text-primary" /> <a href="mailto:{{ $company->email }}" class="hover:text-primary">{{ $company->email }}</a></li>
                @endif
            </ul>
            @if ($socials)
                <p class="mt-5 mb-2 font-semibold text-white">Síguenos</p>
                <div class="flex gap-2">
                    @foreach ($socials as $network => $url)
                        <a href="{{ $url }}" target="_blank" rel="noopener" class="flex size-9 items-center justify-center rounded-full bg-white/10 hover:bg-primary hover:text-on-primary" aria-label="{{ ucfirst($network) }}">
                            <x-store.icon-social :network="$network" class="size-4" />
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="rounded-card bg-white/5 p-5">
            <a href="{{ route('claims.create') }}" class="flex items-center gap-3 font-heading font-bold text-white hover:text-primary">
                <x-heroicon-o-book-open class="size-8 text-primary" /> Libro de Reclamaciones
            </a>
            <p class="mt-4">RUC: {{ $company->ruc }}</p>
            <p>{{ $company->legal_name }}</p>
        </div>
    </div>

    <div class="container-store flex flex-col gap-2 border-t border-white/10 pt-6 text-xs sm:flex-row sm:justify-between">
        <p>© {{ $company->name }}, Todos los derechos reservados.</p>
        <p>Distribuido por <a href="https://vesergenperu.com" target="_blank" rel="noopener" class="text-primary hover:underline">Grupo VesergenPerú</a></p>
    </div>
</footer>
