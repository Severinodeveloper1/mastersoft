<div>
    @if ($filedClaim)
        <div role="status" class="rounded-xl border border-gray-200 p-7">
            <h2 class="text-2xl font-bold">Reclamo registrado – Hoja N.° {{ $filedClaim->code }}</h2>
            <p class="mt-3">Fecha límite de respuesta: {{ $filedClaim->response_due_on->locale('es')->translatedFormat('d \d\e F \d\e Y') }}.</p>
            <p class="mt-2">Se envió una copia a tu correo electrónico.</p>
        </div>
    @else
        <form wire:submit="submit" class="space-y-8">
            <section class="space-y-4 rounded-xl border border-gray-200 p-5">
                <h2 class="text-xl font-bold">1. Identificación del consumidor reclamante</h2>
                <div class="grid gap-4 md:grid-cols-2">
                    <label>Tipo de documento*<select wire:model.live="documentType" class="mt-1 w-full rounded-lg border p-3"><option value="dni">DNI</option><option value="ce">Carné de extranjería</option><option value="passport">Pasaporte</option></select></label>
                    <label>Número de documento*<input wire:model="documentNumber" class="mt-1 w-full rounded-lg border p-3">@error('documentNumber')<span class="text-red-700">{{ $message }}</span>@enderror</label>
                    <label>Nombres*<input wire:model="firstName" class="mt-1 w-full rounded-lg border p-3"></label>
                    <label>Apellido paterno*<input wire:model="paternalSurname" class="mt-1 w-full rounded-lg border p-3"></label>
                    <label>Apellido materno*<input wire:model="maternalSurname" class="mt-1 w-full rounded-lg border p-3"></label>
                    <label>Teléfono / Celular*<div class="mt-1 flex"><span class="border border-r-0 bg-surface-alt p-3">+51</span><input wire:model="phone" class="w-full rounded-r-lg border p-3" inputmode="numeric"></div>@error('phone')<span class="text-red-700">{{ $message }}</span>@enderror</label>
                    <label>Correo electrónico*<input wire:model="email" type="email" class="mt-1 w-full rounded-lg border p-3"></label>
                    <label>Departamento*<select wire:model.live="department" class="mt-1 w-full rounded-lg border p-3"><option value="">Selecciona</option>@foreach ($departments as $item)<option value="{{ $item }}">{{ $item }}</option>@endforeach</select></label>
                    <label>Provincia*<select wire:model.live="province" class="mt-1 w-full rounded-lg border p-3"><option value="">Selecciona</option>@foreach ($provinces as $item)<option value="{{ $item }}">{{ $item }}</option>@endforeach</select></label>
                    <label>Distrito*<select wire:model="district" class="mt-1 w-full rounded-lg border p-3"><option value="">Selecciona</option>@foreach ($districts as $item)<option value="{{ $item }}">{{ $item }}</option>@endforeach</select>@error('district')<span class="text-red-700">{{ $message }}</span>@enderror</label>
                    <label class="md:col-span-2">Dirección*<input wire:model="address" class="mt-1 w-full rounded-lg border p-3"></label>
                </div>
                <label class="flex gap-3"><input wire:model.live="isMinor" type="checkbox" class="mt-1">¿El reclamo es presentado por un menor de edad?</label>
                @if ($isMinor)<div class="grid gap-4 md:grid-cols-2"><label>Nombre del padre, madre o apoderado*<input wire:model="guardianName" class="mt-1 w-full rounded-lg border p-3"></label><label>DNI del apoderado*<input wire:model="guardianDocument" class="mt-1 w-full rounded-lg border p-3" inputmode="numeric"></label></div>@endif
            </section>
            <section class="space-y-4 rounded-xl border border-gray-200 p-5">
                <h2 class="text-xl font-bold">2. Identificación del bien contratado</h2>
                <fieldset class="flex gap-6"><legend class="mb-2 font-semibold">Tipo de bien*</legend><label><input wire:model="goodType" type="radio" value="product"> Producto</label><label><input wire:model="goodType" type="radio" value="service"> Servicio</label></fieldset>
                <div class="grid gap-4 md:grid-cols-2"><label>N.° de pedido o comprobante (opcional)<input wire:model="orderReference" class="mt-1 w-full rounded-lg border p-3"></label><label>Monto reclamado (S/.)*<input wire:model="claimedAmount" type="number" min="0" step="0.01" class="mt-1 w-full rounded-lg border p-3">@error('claimedAmount')<span class="text-red-700">{{ $message }}</span>@enderror</label></div>
                <label class="block">Descripción del producto o servicio*<textarea wire:model="goodDescription" maxlength="1000" rows="3" class="mt-1 w-full rounded-lg border p-3"></textarea></label>
            </section>
            <section class="space-y-4 rounded-xl border border-gray-200 p-5">
                <h2 class="text-xl font-bold">3. Detalle de la reclamación y pedido del consumidor</h2>
                <fieldset class="grid gap-4 md:grid-cols-2"><legend class="mb-2 font-semibold">Tipo de reclamación*</legend><label class="rounded-lg border p-4"><input wire:model="claimType" type="radio" value="complaint"> <strong>Reclamo</strong><span class="mt-2 block">Disconformidad relacionada a los productos o servicios.</span></label><label class="rounded-lg border p-4"><input wire:model="claimType" type="radio" value="grievance"> <strong>Queja</strong><span class="mt-2 block">Malestar o descontento respecto a la atención al público.</span></label></fieldset>
                <label class="block">Detalle*<textarea wire:model="detail" maxlength="1500" rows="5" class="mt-1 w-full rounded-lg border p-3"></textarea><span class="text-sm text-muted">{{ mb_strlen($detail) }}/1500</span>@error('detail')<span class="block text-red-700">{{ $message }}</span>@enderror</label>
                <label class="block">Pedido del consumidor*<textarea wire:model="consumerRequest" maxlength="800" rows="4" class="mt-1 w-full rounded-lg border p-3"></textarea><span class="text-sm text-muted">{{ mb_strlen($consumerRequest) }}/800</span>@error('consumerRequest')<span class="block text-red-700">{{ $message }}</span>@enderror</label>
            </section>
            <section class="space-y-4 rounded-xl bg-surface-alt p-5">
                <h2 class="text-xl font-bold">4. Información importante</h2>
                <p>La formulación del reclamo no impide acudir a otras vías de solución de controversias ni es requisito previo para interponer una denuncia ante el INDECOPI.</p>
                <label class="flex gap-3"><input wire:model="privacyAccepted" type="checkbox" required class="mt-1">Acepto la <a class="underline" href="{{ route('legal.privacy') }}">Política de privacidad</a>*</label>
                @error('privacyAccepted')<span class="text-red-700">{{ $message }}</span>@enderror
                <label class="flex gap-3"><input wire:model="emailNotificationAccepted" type="checkbox" class="mt-1">Acepto recibir la respuesta en mi correo electrónico</label>
                <button type="submit" class="rounded-full bg-primary px-6 py-3 font-semibold text-on-primary">Enviar reclamo</button>
            </section>
        </form>
    @endif
</div>
