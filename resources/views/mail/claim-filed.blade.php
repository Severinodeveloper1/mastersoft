<h1>Reclamo registrado – Hoja N.° {{ $claim->code }}</h1>
<p>Fecha límite de respuesta: {{ $claim->response_due_on->format('d/m/Y') }}</p>
<p>{{ $claim->fullName() }} ({{ $claim->document_type->getLabel() }} {{ $claim->document_number }})</p>
<p>Correo: {{ $claim->email }} · Teléfono: {{ $claim->phone }}</p>
<p>Bien contratado: {{ $claim->good_type->getLabel() }} · Monto: S/. {{ \App\Support\Money::format($claim->claimed_amount) }}</p>
<p>Descripción: {{ $claim->good_description }}</p>
<p>{{ $claim->claim_type->getLabel() }}: {{ $claim->detail }}</p>
<p>Pedido del consumidor: {{ $claim->consumer_request }}</p>
