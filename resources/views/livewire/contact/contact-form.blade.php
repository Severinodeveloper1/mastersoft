<div>
    @if ($sent)<p role="status" class="mb-5 rounded-lg bg-surface-alt p-4 font-semibold">Tu mensaje fue enviado. Gracias por escribirnos.</p>@endif
    <form wire:submit="submit" class="space-y-4">
        <label class="block">Nombre*<input wire:model="name" class="mt-1 w-full rounded-lg border border-gray-300 p-3" autocomplete="name">@error('name')<span class="text-red-700">{{ $message }}</span>@enderror</label>
        <label class="block">Correo*<input wire:model="email" type="email" class="mt-1 w-full rounded-lg border border-gray-300 p-3" autocomplete="email">@error('email')<span class="text-red-700">{{ $message }}</span>@enderror</label>
        <label class="block">Celular*<div class="mt-1 flex"><span class="rounded-l-lg border border-r-0 border-gray-300 bg-surface-alt px-3 py-3">+51</span><input wire:model="phone" inputmode="numeric" class="w-full rounded-r-lg border border-gray-300 p-3" autocomplete="tel-national"></div>@error('phone')<span class="text-red-700">{{ $message }}</span>@enderror</label>
        <label class="block">Asunto*<select wire:model="subject" class="mt-1 w-full rounded-lg border border-gray-300 bg-white p-3"><option value="">Selecciona una opción</option><option>Consulta de producto</option><option>Cotización</option><option>Soporte técnico</option><option>Otro</option></select>@error('subject')<span class="text-red-700">{{ $message }}</span>@enderror</label>
        <label class="block">Mensaje*<textarea wire:model="message" maxlength="500" rows="5" class="mt-1 w-full rounded-lg border border-gray-300 p-3"></textarea><span class="text-sm text-muted">{{ mb_strlen($message) }}/500</span>@error('message')<span class="block text-red-700">{{ $message }}</span>@enderror</label>
        <label class="flex items-start gap-3"><input wire:model="privacyAccepted" type="checkbox" required class="mt-1"><span>Acepto la <a class="underline" href="{{ route('legal.privacy') }}">Política de privacidad</a>.</span></label>
        @error('privacyAccepted')<span class="block text-red-700">{{ $message }}</span>@enderror
        <button class="rounded-full bg-primary px-6 py-3 font-semibold text-on-primary" type="submit">Enviar mensaje</button>
    </form>
</div>
