@php($input = 'mt-1 w-full rounded-input border border-line bg-surface-alt p-3 focus:border-primary focus:ring-2 focus:ring-primary focus:outline-none')

<div>
    @if ($sent)
        <p role="status" class="mb-5 flex items-center gap-2 rounded-input bg-primary/15 p-4 font-semibold text-on-primary">
            <x-heroicon-o-check-circle class="size-5" /> Tu mensaje fue enviado. Gracias por escribirnos.
        </p>
    @endif

    <form wire:submit="submit" class="space-y-4">
        <div class="grid gap-4 sm:grid-cols-2">
            <label class="block text-sm font-semibold">
                Nombre completo <span class="text-accent">*</span>
                <input wire:model="name" class="{{ $input }} font-normal" autocomplete="name">
                @error('name') <span class="text-sm font-normal text-accent">{{ $message }}</span> @enderror
            </label>

            <label class="block text-sm font-semibold">
                Correo electrónico <span class="text-accent">*</span>
                <input wire:model="email" type="email" class="{{ $input }} font-normal" autocomplete="email">
                @error('email') <span class="text-sm font-normal text-accent">{{ $message }}</span> @enderror
            </label>

            <label class="block text-sm font-semibold">
                Celular / WhatsApp <span class="text-accent">*</span>
                <span class="mt-1 flex">
                    <span class="rounded-l-input border border-r-0 border-line bg-surface px-3 py-3 font-normal">+51</span>
                    <input wire:model="phone" inputmode="numeric" class="w-full rounded-r-input border border-line bg-surface-alt p-3 font-normal focus:border-primary focus:ring-2 focus:ring-primary focus:outline-none" autocomplete="tel-national">
                </span>
                @error('phone') <span class="text-sm font-normal text-accent">{{ $message }}</span> @enderror
            </label>

            <label class="block text-sm font-semibold">
                Asunto <span class="text-accent">*</span>
                <select wire:model="subject" class="{{ $input }} font-normal">
                    <option value="">Selecciona un motivo</option>
                    <option>Consulta de producto</option>
                    <option>Cotización</option>
                    <option>Soporte técnico</option>
                    <option>Otro</option>
                </select>
                @error('subject') <span class="text-sm font-normal text-accent">{{ $message }}</span> @enderror
            </label>
        </div>

        <label class="block text-sm font-semibold">
            <span class="flex justify-between">
                <span>Mensaje <span class="text-accent">*</span></span>
                <span class="font-normal text-muted">{{ mb_strlen($message) }} / 500</span>
            </span>
            <textarea wire:model.live.debounce.500ms="message" maxlength="500" rows="5" class="{{ $input }} font-normal"></textarea>
            @error('message') <span class="text-sm font-normal text-accent">{{ $message }}</span> @enderror
        </label>

        <label class="flex items-start gap-3 text-sm">
            <input wire:model="privacyAccepted" type="checkbox" required class="mt-0.5 size-4 accent-primary-dark">
            <span>Acepto la <a class="font-semibold text-primary-dark underline" href="{{ route('legal.privacy') }}" target="_blank">Política de privacidad</a> y el tratamiento de mis datos personales.</span>
        </label>
        @error('privacyAccepted') <span class="block text-sm text-accent">{{ $message }}</span> @enderror

        <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center gap-2 rounded-full bg-primary px-6 py-3 font-semibold text-on-primary hover:bg-primary-hover disabled:opacity-60">
            Enviar mensaje <x-heroicon-o-paper-airplane class="size-4" />
        </button>
    </form>
</div>
