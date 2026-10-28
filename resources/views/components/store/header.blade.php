@php($whatsappUrl = $company->whatsappUrl())

<header class="sticky top-0 z-40 bg-surface shadow-[0_1px_8px_rgba(0,0,0,0.04)]" x-data="{ menuOpen: false, categoriesOpen: false, searchOpen: false }" @open-store-menu.window="menuOpen = true">
    {{-- Top bar --}}
    <div class="bg-footer text-xs text-white/80">
        <div class="container-store flex items-center justify-between gap-4 py-1.5">
            <div class="flex items-center gap-5">
                @if ($company->phone)
                    <span class="flex items-center gap-1.5"><x-heroicon-o-phone class="size-4 text-primary" /> {{ $company->phone }}</span>
                @endif
                <span class="hidden items-center gap-1.5 sm:flex"><x-heroicon-o-truck class="size-4 text-primary" /> Envíos a todo el Perú</span>
            </div>
            <div class="flex items-center gap-4">
                @if ($whatsappUrl)
                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" class="flex items-center gap-1.5 font-semibold text-primary hover:underline">
                        <x-store.icon-whatsapp class="size-4" /> Atención WhatsApp
                    </a>
                @endif
                @if ($company->opening_hours)
                    <span class="hidden md:inline">{{ $company->opening_hours }}</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Main bar --}}
    <div class="container-store flex items-center gap-3 py-3 lg:gap-6">
        <button type="button" class="-ml-1 rounded-input p-1.5 md:hidden" @click="menuOpen = true" aria-label="Abrir menú">
            <x-heroicon-o-bars-3 class="size-6" />
        </button>

        <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2" aria-label="{{ $company->name }} - Inicio">
            @if ($logo = $company->getFirstMediaUrl('logo'))
                <img src="{{ $logo }}" alt="{{ $company->name }}" class="h-9 w-auto">
            @else
                <span class="font-heading text-xl font-bold tracking-tight">{{ $company->name }}</span>
            @endif
        </a>

        <form action="{{ route('store.index') }}" method="GET" role="search"
              class="absolute inset-x-0 top-full bg-surface p-3 shadow-md md:static md:block md:flex-1 md:p-0 md:shadow-none"
              :class="searchOpen ? 'block' : 'hidden'">
            <label class="relative block">
                <span class="sr-only">Buscar producto</span>
                <x-heroicon-o-magnifying-glass class="pointer-events-none absolute top-1/2 left-3 size-5 -translate-y-1/2 text-muted" />
                <input type="search" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar producto por nombre, marca o modelo..."
                       class="w-full rounded-input border-[1.5px] border-line bg-surface py-2.5 pr-24 pl-10 text-sm focus:border-primary focus:ring-2 focus:ring-primary focus:outline-none">
                <button type="submit" class="absolute top-1/2 right-1.5 -translate-y-1/2 rounded-full bg-primary px-4 py-1.5 text-sm font-semibold text-on-primary hover:bg-primary-hover">Buscar</button>
            </label>
        </form>

        <div class="ml-auto flex items-center gap-2 md:ml-0">
            <button type="button" class="rounded-input p-1.5 md:hidden" @click="searchOpen = ! searchOpen" aria-label="Buscar producto">
                <x-heroicon-o-magnifying-glass class="size-6" />
            </button>
            @if ($whatsappUrl)
                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" class="hidden items-center gap-2 rounded-full bg-whatsapp px-4 py-2 text-sm font-semibold text-white hover:brightness-95 lg:flex">
                    <x-store.icon-whatsapp class="size-5" /> WhatsApp
                </a>
            @endif
            <x-store.cart-button />
        </div>
    </div>

    {{-- Desktop navigation --}}
    <nav class="container-store hidden items-center gap-6 pb-2 md:flex" aria-label="Principal">
        <div class="relative" @click.outside="categoriesOpen = false">
            <button type="button" @click="categoriesOpen = ! categoriesOpen" :aria-expanded="categoriesOpen"
                    class="flex items-center gap-2 rounded-input bg-primary px-4 py-2 text-sm font-semibold text-on-primary hover:bg-primary-hover">
                <x-heroicon-o-bars-3 class="size-4" /> Todas las categorías <x-heroicon-o-chevron-down class="size-4" />
            </button>
            <div x-cloak x-show="categoriesOpen" x-transition
                 class="absolute top-full left-0 z-50 mt-2 w-64 rounded-card border border-line bg-surface py-2 shadow-[0_10px_25px_-5px_rgba(0,0,0,0.1)]">
                @foreach ($menuCategories as $category)
                    <a href="{{ route('store.index', ['categoria' => $category->slug]) }}" class="block px-4 py-2 text-sm hover:bg-surface-alt">{{ $category->name }}</a>
                @endforeach
                <a href="{{ route('store.index') }}" class="mt-1 block border-t border-line px-4 pt-2 text-sm font-semibold text-primary-dark">Ver todas las categorías</a>
            </div>
        </div>
        @foreach ([['home', 'Inicio'], ['store.index', 'Tienda'], ['about', 'Nosotros'], ['contact', 'Contáctanos']] as [$route, $label])
            <a href="{{ route($route) }}" @class([
                'text-sm transition-colors hover:text-primary-dark',
                'font-semibold underline decoration-primary decoration-2 underline-offset-8' => request()->routeIs($route),
            ])>{{ $label }}</a>
        @endforeach
    </nav>

    {{-- Mobile menu --}}
    <div x-cloak x-show="menuOpen" class="fixed inset-0 z-50 md:hidden" role="dialog" aria-modal="true" aria-label="Menú">
        <div class="absolute inset-0 bg-black/40" @click="menuOpen = false"></div>
        <div class="absolute inset-y-0 left-0 flex w-80 max-w-[85%] flex-col overflow-y-auto bg-surface p-5" x-show="menuOpen" x-transition:enter="transition" x-transition:enter-start="-translate-x-full">
            <div class="mb-6 flex items-center justify-between">
                <span class="font-heading text-lg font-bold">{{ $company->name }}</span>
                <button type="button" @click="menuOpen = false" aria-label="Cerrar menú"><x-heroicon-o-x-mark class="size-6" /></button>
            </div>
            @foreach ([['home', 'Inicio'], ['store.index', 'Tienda'], ['about', 'Nosotros'], ['contact', 'Contáctanos']] as [$route, $label])
                <a href="{{ route($route) }}" class="border-b border-line py-3 font-semibold">{{ $label }}</a>
            @endforeach
            <p class="mt-6 mb-2 text-xs font-bold tracking-wider text-muted uppercase">Categorías</p>
            @foreach ($menuCategories as $category)
                <a href="{{ route('store.index', ['categoria' => $category->slug]) }}" class="py-2 text-sm">{{ $category->name }}</a>
            @endforeach
        </div>
    </div>
</header>
