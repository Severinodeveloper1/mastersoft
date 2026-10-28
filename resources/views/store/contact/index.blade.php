<x-layouts.store title="Contáctanos" description="Canales de contacto de Mastersoft.">
    <section class="container-store space-y-10 py-12">
        <header><p class="mb-2 font-semibold text-primary-dark">Estamos para ayudarte</p><h1 class="text-4xl font-bold">Contáctanos</h1></header>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @if ($company->address)<article class="rounded-xl border border-gray-200 p-5"><h2 class="font-bold">Dirección</h2><p class="mt-2">{{ $company->address }}</p></article>@endif
            @if ($company->phone || $company->whatsappUrl())<article class="rounded-xl border border-gray-200 p-5"><h2 class="font-bold">Teléfono / WhatsApp</h2>@if ($company->phone)<a class="mt-2 block" href="tel:{{ $company->phone }}">{{ $company->phone }}</a>@endif @if ($company->whatsappUrl())<a href="{{ $company->whatsappUrl() }}">WhatsApp</a>@endif</article>@endif
            @if ($company->email)<article class="rounded-xl border border-gray-200 p-5"><h2 class="font-bold">Correo electrónico</h2><a class="mt-2 block" href="mailto:{{ $company->email }}">{{ $company->email }}</a></article>@endif
            @if ($company->opening_hours)<article class="rounded-xl border border-gray-200 p-5"><h2 class="font-bold">Horario</h2><p class="mt-2">{{ $company->opening_hours }}</p></article>@endif
        </div>
        <div class="grid gap-8 lg:grid-cols-2">
            <section class="rounded-xl border border-gray-200 p-6"><h2 class="mb-5 text-2xl font-bold">Envía tu mensaje</h2><livewire:contact.contact-form /></section>
            @if ($mapUrl)<iframe class="min-h-80 w-full rounded-xl border-0" src="{{ $mapUrl }}" title="Mapa de ubicación" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>@endif
        </div>
    </section>
</x-layouts.store>
