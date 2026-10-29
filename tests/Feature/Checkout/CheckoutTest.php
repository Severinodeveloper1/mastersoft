<?php

namespace Tests\Feature\Checkout;

use App\Enums\CustomerType;
use App\Enums\DeliveryMethod;
use App\Enums\PaymentPreference;
use App\Models\CompanySetting;
use App\Models\Order;
use App\Models\Product;
use App\Services\Cart;
use App\Support\OrderWhatsAppMessage;
use App\Support\Ubigeo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_redirects_when_cart_is_empty(): void
    {
        $this->get(route('checkout.create'))->assertRedirect(route('cart.show'));
    }

    public function test_person_order_requires_privacy_and_valid_peruvian_fields(): void
    {
        $product = Product::factory()->create(['price' => 485]);
        app(Cart::class)->add($product->id, 1);

        Livewire::test('checkout.order-form')
            ->set('first_name', 'Ana')
            ->set('last_name', 'Pérez')
            ->set('document_number', '1234567')
            ->set('phone', '812345678')
            ->set('delivery_method', 'shipping')
            ->set('department', 'San Martín')
            ->set('province', 'Rioja')
            ->set('district', 'Nueva Cajamarca')
            ->set('address', 'Jr. Comercio 100')
            ->set('payment_preference', 'bank_transfer')
            ->call('submit')
            ->assertHasErrors(['document_number', 'phone', 'privacy_accepted']);
    }

    public function test_shipping_rejects_ubigeo_outside_selected_department(): void
    {
        $this->assertFalse(Ubigeo::isValid('San Martín', 'Lima', 'Miraflores'));
        $product = Product::factory()->create();
        app(Cart::class)->add($product->id, 1);

        Livewire::test('checkout.order-form')
            ->set('first_name', 'Ana')
            ->set('last_name', 'Pérez')
            ->set('phone', '912345678')
            ->set('delivery_method', 'shipping')
            ->set('department', 'San Martín')
            ->set('province', 'Lima')
            ->set('district', 'Miraflores')
            ->set('address', 'Jr. Comercio 100')
            ->set('payment_preference', 'bank_transfer')
            ->set('privacy_accepted', true)
            ->call('submit')
            ->assertHasErrors(['province', 'district']);

        $this->assertSame(0, Order::count());
    }

    public function test_company_form_requires_valid_ruc_and_business_contact_fields(): void
    {
        $product = Product::factory()->create();
        app(Cart::class)->add($product->id, 1);

        Livewire::test('checkout.order-form')
            ->set('customer_type', 'company')
            ->set('ruc', '1234567890')
            ->set('phone', '912345678')
            ->set('delivery_method', 'pickup')
            ->set('payment_preference', 'bank_transfer')
            ->set('privacy_accepted', true)
            ->call('submit')
            ->assertHasErrors(['ruc', 'business_name', 'contact_name']);
    }

    public function test_privacy_checkbox_starts_unchecked_and_pickup_does_not_require_address(): void
    {
        $product = Product::factory()->create();
        app(Cart::class)->add($product->id, 1);

        Livewire::test('checkout.order-form')
            ->assertSet('privacy_accepted', false)
            ->set('first_name', 'Ana')
            ->set('last_name', 'Pérez')
            ->set('phone', '912345678')
            ->set('delivery_method', 'pickup')
            ->set('payment_preference', 'bank_transfer')
            ->set('privacy_accepted', true)
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertSame(1, Order::count());
        $this->assertNull(Order::first()->address);
    }

    public function test_company_order_persists_snapshot_and_redirects_to_signed_confirmation(): void
    {
        $product = Product::factory()->create(['name' => 'Adaptador HDMI a VGA', 'price' => 50, 'stock' => 4]);
        app(Cart::class)->add($product->id, 2);

        $component = Livewire::test('checkout.order-form')
            ->set('customer_type', 'company')
            ->set('ruc', '20123456789')
            ->set('business_name', 'Empresa Demo SAC')
            ->set('contact_name', 'Ana Pérez')
            ->set('phone', '912345678')
            ->set('email', 'ana@example.com')
            ->set('delivery_method', 'shipping')
            ->set('department', 'San Martín')
            ->set('province', 'Rioja')
            ->set('district', 'Nueva Cajamarca')
            ->set('address', 'Jr. Comercio 100')
            ->set('payment_preference', 'cash_on_delivery')
            ->set('notes', 'Llamar al llegar')
            ->set('privacy_accepted', true)
            ->call('submit');

        $order = Order::with('items')->firstOrFail();
        $component->assertRedirect(URL::signedRoute('checkout.confirmation', ['order' => $order->code]));
        $this->assertSame('Empresa Demo SAC', $order->business_name);
        $this->assertSame(CustomerType::Company, $order->customer_type);
        $this->assertSame(DeliveryMethod::Shipping, $order->delivery_method);
        $this->assertSame(PaymentPreference::CashOnDelivery, $order->payment_preference);
        $this->assertSame('San Martín', $order->department);
        $this->assertSame('Rioja', $order->province);
        $this->assertSame('Nueva Cajamarca', $order->district);
        $this->assertSame('Jr. Comercio 100', $order->address);
        $this->assertSame('Llamar al llegar', $order->notes);
        $this->assertSame('pending', $order->status->value);
        $this->assertSame('Adaptador HDMI a VGA', $order->items->first()->product_name);
        $this->assertSame('100.00', $order->total);
        $this->assertSame(0, app(Cart::class)->unitCount());
        $this->assertSame(2, $product->fresh()->stock);
    }

    public function test_insufficient_stock_keeps_cart_and_shows_named_error(): void
    {
        $product = Product::factory()->create(['name' => 'Teclado', 'stock' => 2]);
        app(Cart::class)->add($product->id, 2);
        $product->update(['stock' => 1]);

        Livewire::test('checkout.order-form')
            ->set('customer_type', 'person')
            ->set('first_name', 'Ana')
            ->set('last_name', 'Pérez')
            ->set('phone', '912345678')
            ->set('delivery_method', 'pickup')
            ->set('payment_preference', 'bank_transfer')
            ->set('privacy_accepted', true)
            ->call('submit')
            ->assertSee('Stock insuficiente')
            ->assertSee('Teclado');

        $this->assertSame(2, app(Cart::class)->unitCount());
        $this->assertSame(1, $product->fresh()->stock);
        $this->assertSame(0, Order::count());
    }

    public function test_stock_dropping_to_zero_during_checkout_reports_error_and_preserves_cart(): void
    {
        $product = Product::factory()->create(['name' => 'Teclado', 'stock' => 2]);
        app(Cart::class)->add($product->id, 2);

        $component = Livewire::test('checkout.order-form')
            ->set('customer_type', 'person')
            ->set('first_name', 'Ana')
            ->set('last_name', 'Pérez')
            ->set('phone', '912345678')
            ->set('delivery_method', 'pickup')
            ->set('payment_preference', 'bank_transfer')
            ->set('privacy_accepted', true);

        $product->update(['stock' => 0]);
        $this->assertSame([$product->id => 2], session('store_cart'));

        $component->call('submit')
            ->assertSee('Stock insuficiente')
            ->assertSee('Teclado')
            ->assertSet('preserve_sold_out_cart', true);

        $this->assertSame([$product->id => 2], session('store_cart'));
        $this->assertSame(0, Order::count());
    }

    public function test_confirmation_requires_signature_and_whatsapp_message_uses_code_without_sku(): void
    {
        $order = Order::factory()->create(['subtotal' => 84.75, 'tax' => 15.25, 'total' => 100]);
        $order->items()->create([
            'product_name' => 'Adaptador HDMI a VGA',
            'unit_price' => 50,
            'quantity' => 2,
            'line_total' => 100,
        ]);

        $this->get(route('checkout.confirmation', $order->code))->assertForbidden();
        $this->get(URL::signedRoute('checkout.confirmation', ['order' => $order->code]))
            ->assertOk()->assertSee('¡Pedido registrado!')->assertSee($order->code);

        $message = OrderWhatsAppMessage::make($order->load('items'));
        $this->assertStringContainsString('Hola Mastersoft, acabo de registrar el pedido '.$order->code, $message);
        $this->assertStringContainsString('2 x Adaptador HDMI a VGA - S/. 50.00', $message);
        $this->assertStringContainsString('Total: S/. 100.00', $message);
        $this->assertStringNotContainsString('SKU', $message);

        CompanySetting::current()->update(['whatsapp_number' => '+51 999 111 222']);
        $url = CompanySetting::current()->whatsappUrl($message);
        $this->assertStringStartsWith('https://wa.me/51999111222?text=', $url);
        $this->assertStringContainsString(rawurlencode($message), $url);
    }

    public function test_confirmation_falls_back_to_phone_when_whatsapp_is_not_configured(): void
    {
        CompanySetting::current()->update(['whatsapp_number' => null, 'phone' => '+51 942 555 111']);
        $order = Order::factory()->create();

        $this->get(URL::signedRoute('checkout.confirmation', ['order' => $order->code]))
            ->assertOk()
            ->assertSee($order->code)
            ->assertSee('tel:+51942555111', false)
            ->assertDontSee('href=""', false);
    }
}
