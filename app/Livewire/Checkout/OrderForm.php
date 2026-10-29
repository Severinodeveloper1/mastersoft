<?php

namespace App\Livewire\Checkout;

use App\Enums\CustomerType;
use App\Enums\DeliveryMethod;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Services\Cart;
use App\Support\Ubigeo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Livewire\Component;

class OrderForm extends Component
{
    public string $customer_type = 'person';

    public string $first_name = '';

    public string $last_name = '';

    public string $document_number = '';

    public string $ruc = '';

    public string $business_name = '';

    public string $contact_name = '';

    public string $phone = '';

    public string $email = '';

    public string $delivery_method = 'shipping';

    public string $department = '';

    public string $province = '';

    public string $district = '';

    public string $address = '';

    public string $reference = '';

    public string $payment_preference = 'bank_transfer';

    public string $notes = '';

    public bool $privacy_accepted = false;

    public bool $preserve_sold_out_cart = false;

    public function updatedCustomerType(): void
    {
        $this->resetValidation();
    }

    public function updatedDeliveryMethod(): void
    {
        if ($this->delivery_method === DeliveryMethod::Pickup->value) {
            $this->reset('department', 'province', 'district', 'address', 'reference');
        }

        $this->resetValidation();
    }

    public function updatedDepartment(): void
    {
        $this->reset('province', 'district');
    }

    public function updatedProvince(): void
    {
        $this->reset('district');
    }

    public function departments(): array
    {
        return Ubigeo::departments();
    }

    public function provinces(): array
    {
        return Ubigeo::provinces($this->department);
    }

    public function districts(): array
    {
        return Ubigeo::districts($this->department, $this->province);
    }

    public function lines(): array
    {
        return app(Cart::class)->lines(
            preserveSoldOut: $this->preserve_sold_out_cart || $this->getErrorBag()->has('cart'),
        );
    }

    public function subtotal(): float
    {
        return round($this->total() / 1.18, 2);
    }

    public function tax(): float
    {
        return round($this->total() - $this->subtotal(), 2);
    }

    public function total(): float
    {
        return round(array_sum(array_column($this->lines(), 'line_total')), 2);
    }

    public function submit(): void
    {
        $data = $this->validate($this->rules(), [
            'required' => 'Este campo es obligatorio.',
            'email' => 'Ingresa un correo electrónico válido.',
            'digits' => 'El campo debe tener :digits dígitos.',
            'accepted' => 'Debes aceptar la Política de privacidad.',
            'in' => 'Selecciona una opción válida.',
            'regex' => 'Ingresa un celular peruano válido de 9 dígitos que empiece con 9.',
        ], [
            'first_name' => 'Nombres',
            'last_name' => 'Apellidos',
            'document_number' => 'DNI',
            'ruc' => 'RUC',
            'business_name' => 'Razón social',
            'contact_name' => 'Persona de contacto',
            'phone' => 'Celular / WhatsApp',
            'email' => 'Correo electrónico',
            'department' => 'Departamento',
            'province' => 'Provincia',
            'district' => 'Distrito',
            'address' => 'Dirección o agencia',
            'privacy_accepted' => 'Política de privacidad',
        ]);

        $cart = app(Cart::class);
        $lines = $cart->checkoutLines();

        if ($lines === []) {
            $this->redirectRoute('cart.show');

            return;
        }

        $order = DB::transaction(function () use ($data, $lines): ?Order {
            $productIds = array_map(fn (array $line): int => $line['product']->id, $lines);
            $products = Product::query()->whereKey($productIds)->lockForUpdate()->get()->keyBy('id');
            $snapshots = [];

            foreach ($lines as $line) {
                $product = $products->get($line['product']->id);
                if (! $product || ! $product->is_active || $product->stock < $line['quantity']) {
                    $this->preserve_sold_out_cart = $product?->is_active ?? false;
                    $this->addError('cart', 'Stock insuficiente para '.($product?->name ?? $line['product']->name).'.');

                    return null;
                }
                $snapshots[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => (float) $product->price,
                    'quantity' => $line['quantity'],
                    'line_total' => round((float) $product->price * $line['quantity'], 2),
                ];
            }

            $total = round(array_sum(array_column($snapshots, 'line_total')), 2);
            $subtotal = round($total / 1.18, 2);
            $order = Order::create([
                'status' => OrderStatus::Pending,
                'customer_type' => $data['customer_type'],
                'first_name' => $this->customer_type === CustomerType::Person->value ? $data['first_name'] : null,
                'last_name' => $this->customer_type === CustomerType::Person->value ? $data['last_name'] : null,
                'document_number' => $this->customer_type === CustomerType::Person->value ? ($data['document_number'] ?: null) : null,
                'ruc' => $this->customer_type === CustomerType::Company->value ? $data['ruc'] : null,
                'business_name' => $this->customer_type === CustomerType::Company->value ? $data['business_name'] : null,
                'contact_name' => $this->customer_type === CustomerType::Company->value ? $data['contact_name'] : null,
                'phone' => $data['phone'],
                'email' => $data['email'] ?: null,
                'delivery_method' => $data['delivery_method'],
                'department' => $data['delivery_method'] === DeliveryMethod::Shipping->value ? $data['department'] : null,
                'province' => $data['delivery_method'] === DeliveryMethod::Shipping->value ? $data['province'] : null,
                'district' => $data['delivery_method'] === DeliveryMethod::Shipping->value ? $data['district'] : null,
                'address' => $data['delivery_method'] === DeliveryMethod::Shipping->value ? $data['address'] : null,
                'reference' => $data['delivery_method'] === DeliveryMethod::Shipping->value ? ($data['reference'] ?: null) : null,
                'payment_preference' => $data['payment_preference'],
                'notes' => $data['notes'] ?: null,
                'subtotal' => $subtotal,
                'tax' => round($total - $subtotal, 2),
                'total' => $total,
                'privacy_accepted_at' => now(),
            ]);

            foreach ($snapshots as $snapshot) {
                $order->items()->create($snapshot);
            }

            return $order;
        });

        if ($order) {
            $cart->clear();
            $this->redirect(URL::signedRoute('checkout.confirmation', ['order' => $order->code]));
        }
    }

    protected function rules(): array
    {
        $rules = [
            'customer_type' => ['required', 'in:person,company'],
            'phone' => ['required', 'regex:/^9\d{8}$/'],
            'email' => ['nullable', 'email'],
            'delivery_method' => ['required', 'in:shipping,pickup'],
            'payment_preference' => ['required', 'in:bank_transfer,cash_on_delivery'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'privacy_accepted' => ['accepted'],
        ];

        if ($this->customer_type === CustomerType::Person->value) {
            $rules += [
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'document_number' => ['nullable', 'digits:8'],
            ];
        } else {
            $rules += [
                'ruc' => ['required', 'digits:11'],
                'business_name' => ['required', 'string', 'max:255'],
                'contact_name' => ['required', 'string', 'max:255'],
            ];
        }

        if ($this->delivery_method === DeliveryMethod::Shipping->value) {
            $rules += [
                'department' => ['required', 'string', Rule::in(Ubigeo::departments())],
                'province' => ['required', 'string', Rule::in(Ubigeo::provinces($this->department))],
                'district' => ['required', 'string', Rule::in(Ubigeo::districts($this->department, $this->province))],
                'address' => ['required', 'string', 'max:255'],
                'reference' => ['nullable', 'string', 'max:255'],
            ];
        }

        return $rules;
    }

    public function render(): mixed
    {
        return view('livewire.checkout.order-form');
    }
}
