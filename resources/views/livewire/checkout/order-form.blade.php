<section class="container-store py-8">
    <nav class="mb-4 text-sm text-muted" aria-label="Ruta de navegación">
        <a href="{{ route('cart.show') }}" class="hover:text-primary-dark">Carrito</a>
        <span class="px-2">/</span>
        <span class="text-ink">Enviar pedido por WhatsApp</span>
    </nav>

    <div class="mb-7">
        <h1 class="font-heading text-3xl font-bold">Enviar pedido por WhatsApp</h1>
        <p class="mt-2 text-sm text-muted">Completa tus datos para registrar tu pedido y continuar por WhatsApp.</p>
    </div>

    <form wire:submit="submit" class="grid items-start gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <fieldset class="rounded-card border border-line bg-surface p-5 shadow-card sm:p-6">
                <legend class="px-2 font-heading text-xl font-bold">1. Tus datos</legend>
                <div class="mb-5 flex gap-3">
                    <label class="flex cursor-pointer items-center gap-2 rounded-full border border-line bg-surface-alt px-4 py-2 text-sm">
                        <input type="radio" value="person" wire:model.live="customer_type" class="accent-primary-dark">
                        Persona natural
                    </label>
                    <label class="flex cursor-pointer items-center gap-2 rounded-full border border-line bg-surface-alt px-4 py-2 text-sm">
                        <input type="radio" value="company" wire:model.live="customer_type" class="accent-primary-dark">
                        Empresa
                    </label>
                </div>

                @if ($customer_type === 'person')
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="first_name" class="mb-1 block text-sm font-semibold">Nombres <span class="text-accent">*</span></label>
                            <input id="first_name" type="text" wire:model="first_name" autocomplete="given-name" class="w-full rounded-input border border-line bg-surface px-3 py-2.5 focus:border-primary-dark focus:outline-none">
                            @error('first_name') <p class="mt-1 text-xs text-accent">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="last_name" class="mb-1 block text-sm font-semibold">Apellidos <span class="text-accent">*</span></label>
                            <input id="last_name" type="text" wire:model="last_name" autocomplete="family-name" class="w-full rounded-input border border-line bg-surface px-3 py-2.5 focus:border-primary-dark focus:outline-none">
                            @error('last_name') <p class="mt-1 text-xs text-accent">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="document_number" class="mb-1 block text-sm font-semibold">DNI <span class="font-normal text-muted">(opcional, 8 dígitos)</span></label>
                            <input id="document_number" type="text" inputmode="numeric" maxlength="8" wire:model="document_number" class="w-full rounded-input border border-line bg-surface px-3 py-2.5 focus:border-primary-dark focus:outline-none">
                            @error('document_number') <p class="mt-1 text-xs text-accent">{{ $message }}</p> @enderror
                        </div>
                    </div>
                @else
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="ruc" class="mb-1 block text-sm font-semibold">RUC <span class="text-accent">*</span></label>
                            <input id="ruc" type="text" inputmode="numeric" maxlength="11" wire:model="ruc" class="w-full rounded-input border border-line bg-surface px-3 py-2.5 focus:border-primary-dark focus:outline-none">
                            @error('ruc') <p class="mt-1 text-xs text-accent">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="business_name" class="mb-1 block text-sm font-semibold">Razón social <span class="text-accent">*</span></label>
                            <input id="business_name" type="text" wire:model="business_name" class="w-full rounded-input border border-line bg-surface px-3 py-2.5 focus:border-primary-dark focus:outline-none">
                            @error('business_name') <p class="mt-1 text-xs text-accent">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="contact_name" class="mb-1 block text-sm font-semibold">Persona de contacto <span class="text-accent">*</span></label>
                            <input id="contact_name" type="text" wire:model="contact_name" class="w-full rounded-input border border-line bg-surface px-3 py-2.5 focus:border-primary-dark focus:outline-none">
                            @error('contact_name') <p class="mt-1 text-xs text-accent">{{ $message }}</p> @enderror
                        </div>
                    </div>
                @endif

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="phone" class="mb-1 block text-sm font-semibold">Celular / WhatsApp (+51) <span class="text-accent">*</span></label>
                        <input id="phone" type="tel" inputmode="numeric" maxlength="9" wire:model="phone" autocomplete="tel-national" class="w-full rounded-input border border-line bg-surface px-3 py-2.5 focus:border-primary-dark focus:outline-none">
                        @error('phone') <p class="mt-1 text-xs text-accent">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="email" class="mb-1 block text-sm font-semibold">Correo electrónico <span class="font-normal text-muted">(opcional)</span></label>
                        <input id="email" type="email" wire:model="email" autocomplete="email" class="w-full rounded-input border border-line bg-surface px-3 py-2.5 focus:border-primary-dark focus:outline-none">
                        @error('email') <p class="mt-1 text-xs text-accent">{{ $message }}</p> @enderror
                    </div>
                </div>
            </fieldset>

            <fieldset class="rounded-card border border-line bg-surface p-5 shadow-card sm:p-6">
                <legend class="px-2 font-heading text-xl font-bold">2. Modalidad de entrega</legend>
                <div class="space-y-3">
                    <label class="flex cursor-pointer items-center gap-3 rounded-input border border-line bg-surface-alt p-3">
                        <input type="radio" value="shipping" wire:model.live="delivery_method" class="accent-primary-dark">
                        <span class="text-sm font-semibold">Envío a domicilio o agencia</span>
                    </label>
                    <label class="flex cursor-pointer items-center gap-3 rounded-input border border-line bg-surface-alt p-3">
                        <input type="radio" value="pickup" wire:model.live="delivery_method" class="accent-primary-dark">
                        <span class="text-sm font-semibold">Recojo en tienda</span>
                    </label>
                </div>

                @if ($delivery_method === 'shipping')
                    <div class="mt-4 grid gap-4 sm:grid-cols-3">
                        <div>
                            <label for="department" class="mb-1 block text-sm font-semibold">Departamento <span class="text-accent">*</span></label>
                            <select id="department" wire:model.live="department" class="w-full rounded-input border border-line bg-surface px-3 py-2.5">
                                <option value="">Selecciona</option>
                                @foreach ($this->departments() as $departmentOption)
                                    <option value="{{ $departmentOption }}">{{ $departmentOption }}</option>
                                @endforeach
                            </select>
                            @error('department') <p class="mt-1 text-xs text-accent">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="province" class="mb-1 block text-sm font-semibold">Provincia <span class="text-accent">*</span></label>
                            <select id="province" wire:model.live="province" @disabled($department === '') class="w-full rounded-input border border-line bg-surface px-3 py-2.5">
                                <option value="">Selecciona</option>
                                @foreach ($this->provinces() as $provinceOption)
                                    <option value="{{ $provinceOption }}">{{ $provinceOption }}</option>
                                @endforeach
                            </select>
                            @error('province') <p class="mt-1 text-xs text-accent">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="district" class="mb-1 block text-sm font-semibold">Distrito <span class="text-accent">*</span></label>
                            <select id="district" wire:model="district" @disabled($province === '') class="w-full rounded-input border border-line bg-surface px-3 py-2.5">
                                <option value="">Selecciona</option>
                                @foreach ($this->districts() as $districtOption)
                                    <option value="{{ $districtOption }}">{{ $districtOption }}</option>
                                @endforeach
                            </select>
                            @error('district') <p class="mt-1 text-xs text-accent">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="address" class="mb-1 block text-sm font-semibold">Dirección o agencia <span class="text-accent">*</span></label>
                            <input id="address" type="text" wire:model="address" class="w-full rounded-input border border-line bg-surface px-3 py-2.5">
                            @error('address') <p class="mt-1 text-xs text-accent">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="reference" class="mb-1 block text-sm font-semibold">Referencia <span class="font-normal text-muted">(opcional)</span></label>
                            <input id="reference" type="text" wire:model="reference" class="w-full rounded-input border border-line bg-surface px-3 py-2.5">
                        </div>
                    </div>
                @endif
            </fieldset>

            <fieldset class="rounded-card border border-line bg-surface p-5 shadow-card sm:p-6">
                <legend class="px-2 font-heading text-xl font-bold">3. Forma de pago preferida</legend>
                <div class="grid gap-3 sm:grid-cols-2">
                    <label class="flex cursor-pointer items-center gap-3 rounded-input border border-line bg-surface-alt p-4">
                        <input type="radio" value="bank_transfer" wire:model="payment_preference" class="accent-primary-dark">
                        <span class="text-sm font-semibold">Transferencia bancaria</span>
                    </label>
                    <label class="flex cursor-pointer items-center gap-3 rounded-input border border-line bg-surface-alt p-4">
                        <input type="radio" value="cash_on_delivery" wire:model="payment_preference" class="accent-primary-dark">
                        <span class="text-sm font-semibold">Pago contra entrega</span>
                    </label>
                </div>
            </fieldset>

            <fieldset class="rounded-card border border-line bg-surface p-5 shadow-card sm:p-6">
                <legend class="px-2 font-heading text-xl font-bold">4. Notas del pedido (opcional)</legend>
                <label for="notes" class="sr-only">Notas del pedido</label>
                <textarea id="notes" rows="3" wire:model="notes" class="w-full rounded-input border border-line bg-surface px-3 py-2.5" placeholder="¿Algo más que debamos saber?"></textarea>
            </fieldset>
        </div>

        <aside class="rounded-card border border-line bg-surface p-5 shadow-card lg:sticky lg:top-40">
            <h2 class="font-heading text-xl font-bold">Tu pedido</h2>
            <div class="mt-4 divide-y divide-line">
                @foreach ($this->lines() as $line)
                    <div wire:key="checkout-line-{{ $line['product']->id }}" class="flex justify-between gap-3 py-3 text-sm">
                        <span class="min-w-0">{{ $line['quantity'] }} × {{ $line['product']->name }}</span>
                        <span class="price">{{ \App\Support\Money::format($line['line_total']) }}</span>
                    </div>
                @endforeach
            </div>
            <dl class="mt-4 space-y-3 border-t border-line pt-4 text-sm">
                <div class="flex justify-between gap-3"><dt>Subtotal (sin IGV)</dt><dd class="price">{{ \App\Support\Money::format($this->subtotal()) }}</dd></div>
                <div class="flex justify-between gap-3"><dt>IGV (18%)</dt><dd class="price">{{ \App\Support\Money::format($this->tax()) }}</dd></div>
                <div class="flex justify-between gap-3"><dt>Envío</dt><dd>Por coordinar</dd></div>
                <div class="flex justify-between gap-3 border-t border-line pt-4 text-base font-bold"><dt>Total</dt><dd class="price">{{ \App\Support\Money::format($this->total()) }}</dd></div>
            </dl>
            <p class="mt-3 text-xs text-muted">Los precios incluyen IGV</p>

            @error('cart')
                <p class="mt-4 rounded-input bg-accent/10 p-3 text-sm font-semibold text-accent">{{ $message }}</p>
            @enderror

            <label class="mt-5 flex items-start gap-3 text-sm">
                <input type="checkbox" wire:model="privacy_accepted" class="mt-1 size-4 shrink-0 accent-primary-dark">
                <span>Acepto la <a href="{{ route('legal.privacy') }}" target="_blank" class="font-semibold text-primary-dark underline">Política de privacidad</a> <span class="text-accent">*</span></span>
            </label>
            @error('privacy_accepted') <p class="mt-1 text-xs text-accent">{{ $message }}</p> @enderror

            <button type="submit" wire:loading.attr="disabled" class="mt-5 flex w-full items-center justify-center rounded-full bg-primary px-5 py-3 font-bold text-on-primary hover:bg-primary-hover disabled:opacity-60">
                Enviar pedido por WhatsApp
            </button>
            <p class="mt-3 text-center text-xs leading-relaxed text-muted">Se abrirá WhatsApp con el detalle de tu pedido para que lo confirmes.</p>
        </aside>
    </form>

    <footer class="mt-8 border-t border-line pt-4 text-center text-xs text-muted">
        <a href="{{ route('claims.create') }}" class="underline">Libro de Reclamaciones</a>
    </footer>
</section>
