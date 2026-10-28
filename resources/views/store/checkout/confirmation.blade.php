<x-layouts.store title="Pedido registrado">
    <section class="container-store max-w-5xl py-8 md:py-12">
        <div class="mx-auto max-w-2xl text-center">
            <div class="mx-auto flex size-16 items-center justify-center rounded-full bg-primary/20">
                <span class="flex size-11 items-center justify-center rounded-full bg-primary text-on-primary">
                    <x-heroicon-o-check class="size-7" />
                </span>
            </div>
            <h1 class="mt-5 font-heading text-4xl font-bold tracking-tight sm:text-5xl">¡Pedido registrado!</h1>
            <p class="mt-3 text-base leading-relaxed text-muted">Te escribiremos por WhatsApp para confirmar stock, envío y pago.</p>
            <div class="mt-5 inline-flex flex-wrap items-center justify-center gap-2 rounded-full bg-surface-alt px-5 py-3">
                <span class="font-heading text-lg font-bold">{{ $order->code }}</span>
                <span class="text-muted">·</span>
                <time datetime="{{ $order->created_at->toIso8601String() }}" class="text-sm text-muted">{{ $order->created_at->translatedFormat('d F Y, H:i') }}</time>
            </div>
        </div>

        <div class="mx-auto mt-7 max-w-3xl rounded-card border border-line bg-surface p-5 shadow-card sm:flex sm:items-center sm:justify-between sm:gap-5">
            <div>
                <h2 class="font-heading text-lg font-bold">Continúa por WhatsApp</h2>
                <p class="mt-1 text-sm text-muted">Envía el detalle del pedido para que podamos confirmarlo.</p>
            </div>
            @if ($whatsappUrl)
                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-full bg-whatsapp px-5 py-3 font-bold text-white hover:brightness-95 sm:mt-0 sm:w-auto">
                    <x-store.icon-whatsapp class="size-5" /> Enviar pedido por WhatsApp
                </a>
            @else
                <div class="mt-4 text-sm sm:mt-0 sm:text-right">
                    <p class="font-semibold">Tu código de pedido es {{ $order->code }}.</p>
                    @if ($company->phone)
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $company->phone) }}" class="mt-1 inline-block font-semibold text-primary-dark underline">Llamar al {{ $company->phone }}</a>
                    @else
                        <a href="{{ route('contact') }}" class="mt-1 inline-block font-semibold text-primary-dark underline">Contactar a Mastersoft</a>
                    @endif
                </div>
            @endif
        </div>

        <section class="mt-8">
            <h2 class="mb-4 font-heading text-xl font-bold">Próximos pasos</h2>
            <div class="grid gap-4 md:grid-cols-3">
                @foreach ([
                    ['1', 'Envía el mensaje', 'Comparte el detalle de tu pedido con nuestro equipo por WhatsApp.'],
                    ['2', 'Confirmamos disponibilidad', 'Te escribiremos para confirmar stock y coordinar la entrega.'],
                    ['3', 'Coordinamos el pago', 'Acordaremos contigo la forma de pago que elegiste.'],
                ] as [$number, $title, $description])
                    <article class="rounded-card border border-line bg-surface p-4 shadow-card">
                        <div class="flex items-center gap-3">
                            <span class="flex size-7 items-center justify-center rounded-full bg-primary text-sm font-bold text-on-primary">{{ $number }}</span>
                            <h3 class="font-semibold">{{ $title }}</h3>
                        </div>
                        <p class="mt-3 text-sm leading-relaxed text-muted">{{ $description }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <div class="mt-8 grid gap-6 md:grid-cols-2">
            <section class="rounded-card border border-line bg-surface p-5 shadow-card">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="font-heading text-lg font-bold">Productos del pedido</h2>
                    <span class="text-xs text-muted">{{ $order->items->sum('quantity') }} unidades</span>
                </div>
                <div class="mt-3 divide-y divide-line">
                    @foreach ($order->items as $item)
                        <div class="flex items-center justify-between gap-4 py-3 text-sm">
                            <div class="min-w-0">
                                <p class="font-semibold">{{ $item->product_name }}</p>
                                <p class="text-muted">{{ $item->quantity }} × {{ \App\Support\Money::format($item->unit_price) }}</p>
                            </div>
                            <span class="price">{{ \App\Support\Money::format($item->line_total) }}</span>
                        </div>
                    @endforeach
                </div>
            </section>

            <div class="space-y-6">
                <section class="rounded-card border border-line bg-surface p-5 shadow-card">
                    <h2 class="font-heading text-lg font-bold">Entrega</h2>
                    <p class="mt-2 text-sm font-semibold">{{ $order->delivery_method->getLabel() }}</p>
                    @if ($order->delivery_method->value === 'shipping')
                        <p class="mt-1 text-sm text-muted">{{ $order->address }}<br>{{ $order->district }}, {{ $order->province }}, {{ $order->department }}</p>
                        @if ($order->reference)
                            <p class="mt-1 text-sm text-muted">Referencia: {{ $order->reference }}</p>
                        @endif
                    @endif
                    <p class="mt-3 text-sm">Forma de pago preferida: <span class="font-semibold">{{ $order->payment_preference->getLabel() }}</span></p>
                    @if ($order->notes)
                        <p class="mt-2 text-sm text-muted">Notas: {{ $order->notes }}</p>
                    @endif
                </section>

                <section class="rounded-card border border-line bg-surface p-5 shadow-card">
                    <h2 class="font-heading text-lg font-bold">Resumen del pedido</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex justify-between gap-4"><dt>Subtotal (sin IGV)</dt><dd class="price">{{ \App\Support\Money::format($order->subtotal) }}</dd></div>
                        <div class="flex justify-between gap-4"><dt>IGV (18%)</dt><dd class="price">{{ \App\Support\Money::format($order->tax) }}</dd></div>
                        <div class="flex justify-between gap-4"><dt>Envío</dt><dd>Por coordinar</dd></div>
                        <div class="flex justify-between gap-4 border-t border-line pt-4 text-base font-bold"><dt>Total</dt><dd class="price">{{ \App\Support\Money::format($order->total) }}</dd></div>
                    </dl>
                </section>
            </div>
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('store.index') }}" class="inline-flex rounded-full border border-line px-6 py-3 text-sm font-semibold hover:bg-surface-alt">Seguir comprando</a>
        </div>
    </section>
</x-layouts.store>
