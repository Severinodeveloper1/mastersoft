<?php

namespace Tests\Feature\Erp;

use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\Pages\ViewOrder;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\Cart;
use App\Services\Erp\QuotationResult;
use App\Services\Erp\QuotationSender;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Defer\DeferredCallbackCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class QuotationSenderTest extends TestCase
{
    use RefreshDatabase;

    private const BASE = 'https://erp.test/api/v1';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.erp' => [
            'base_url' => self::BASE,
            'tenant' => 'delta',
            'username' => 'web-reader',
            'password' => 'secret',
            'application' => 'W',
            'device' => 'Mastersoft sync',
            'per_page' => 2,
            'timeout' => 5,
            'sync_time' => '03:00',
            'quotation' => [
                'customer_id' => 3,
                'currency_id' => 469,
                'payment_condition_id' => 201,
                'days' => 15,
            ],
        ]]);
    }

    public function test_checkout_answers_the_customer_before_calling_the_erp(): void
    {
        $this->fakeErp();
        $product = Product::factory()->create(['external_id' => '14', 'stock' => 6]);
        app(Cart::class)->add($product->id, 1);

        $component = $this->placeOrder(runDeferred: false);

        // The send is deferred until the response has gone out, so a slow ERP never blocks checkout.
        $order = Order::firstOrFail();
        $component->assertRedirect(URL::signedRoute('checkout.confirmation', ['order' => $order->code]));
        $this->assertSame(0, app(Cart::class)->unitCount());
        Http::assertNothingSent();
        $this->assertCount(1, app(DeferredCallbackCollection::class));

        app(DeferredCallbackCollection::class)->invoke();

        $this->assertSame(OrderStatus::SentToErp, $order->fresh()->status);
    }

    public function test_placed_order_is_sent_to_the_erp_as_a_quotation_and_marked_as_sent(): void
    {
        $this->fakeErp();
        $axe = Product::factory()->create(['external_id' => '14', 'name' => 'AXE BARRA', 'price' => 18, 'stock' => 6]);
        $coca = Product::factory()->create(['external_id' => '7', 'name' => 'COCA COLA', 'price' => 3.5, 'stock' => 10]);
        app(Cart::class)->add($axe->id, 2);
        app(Cart::class)->add($coca->id, 1);

        $component = $this->placeOrder();

        $order = Order::firstOrFail();
        $component->assertRedirect(URL::signedRoute('checkout.confirmation', ['order' => $order->code]));
        Http::assertSent(fn (Request $request) => $request->url() === self::BASE.'/cotizaciones'
            && $request->method() === 'POST'
            && $request->hasHeader('Authorization', 'Bearer 1|test-token')
            && $request->hasHeader('X-Tenant-Id', 'delta')
            && $request['idcliente'] === 3
            && $request['idmoneda'] === 469
            && $request['idcondicionpago'] === 201
            && $request['dias'] === 15
            && $request['reservarstock'] === false
            && str_starts_with($request['observacion'], "DESDE TIENDA WEB - {$order->code} - Ana Pérez - Cel. 912345678")
            && str_contains($request['observacion'], 'Llamar al llegar')
            && $request['items'] === [
                ['idproducto' => 14, 'cantidad' => 2, 'preciounitario' => 18.0],
                ['idproducto' => 7, 'cantidad' => 1, 'preciounitario' => 3.5],
            ]);
        $this->assertSame(OrderStatus::SentToErp, $order->status);
        $this->assertSame(2, $order->erp_quotation_id);
        $this->assertSame('CT01-00000002', $order->erp_quotation_number);
        $this->assertNotNull($order->erp_sent_at);
        $this->assertNull($order->erp_error);
    }

    public function test_a_failed_send_keeps_the_order_pending_and_the_customer_still_sees_the_confirmation(): void
    {
        $this->fakeErp(quotation: Http::response(['status' => 'error', 'message' => 'Cliente no existe'], 422));
        $product = Product::factory()->create(['external_id' => '14', 'stock' => 6]);
        app(Cart::class)->add($product->id, 1);

        $component = $this->placeOrder();

        $order = Order::firstOrFail();
        $component->assertRedirect(URL::signedRoute('checkout.confirmation', ['order' => $order->code]));
        $this->assertSame(OrderStatus::Pending, $order->status);
        $this->assertNull($order->erp_quotation_id);
        $this->assertStringContainsString('HTTP 422', $order->erp_error);
        $this->assertSame(0, app(Cart::class)->unitCount());
    }

    public function test_a_success_answer_without_a_quotation_id_keeps_the_order_pending(): void
    {
        $this->fakeErp(quotation: Http::response(['status' => 'success', 'data' => ['numero' => null]]));
        $product = Product::factory()->create(['external_id' => '14', 'stock' => 6]);
        app(Cart::class)->add($product->id, 1);

        $this->placeOrder();

        $order = Order::firstOrFail();
        $this->assertSame(OrderStatus::Pending, $order->status);
        $this->assertNull($order->erp_sent_at);
        $this->assertStringContainsString('quotation id', $order->erp_error);
    }

    public function test_an_unreachable_erp_keeps_the_order_pending(): void
    {
        $this->freshHttp();
        Http::fake(fn () => throw new ConnectionException('timeout'));
        $product = Product::factory()->create(['external_id' => '14', 'stock' => 6]);
        app(Cart::class)->add($product->id, 1);

        $this->placeOrder()->assertRedirect();

        $order = Order::firstOrFail();
        $this->assertSame(OrderStatus::Pending, $order->status);
        $this->assertStringContainsString('could not connect', $order->erp_error);
    }

    public function test_the_quotation_request_is_never_retried_so_it_cannot_be_duplicated(): void
    {
        $this->fakeErp(quotation: Http::response(['status' => 'error'], 500));
        $product = Product::factory()->create(['external_id' => '14', 'stock' => 6]);
        app(Cart::class)->add($product->id, 1);

        $this->placeOrder();

        Http::assertSentCount(2);
        $this->assertSame(OrderStatus::Pending, Order::firstOrFail()->status);
    }

    public function test_an_order_with_a_product_not_in_the_erp_stays_pending_without_calling_the_erp(): void
    {
        $this->fakeErp();
        $product = Product::factory()->create(['external_id' => null, 'name' => 'Producto manual', 'stock' => 6]);
        app(Cart::class)->add($product->id, 1);

        $this->placeOrder();

        Http::assertNothingSent();
        $order = Order::firstOrFail();
        $this->assertSame(OrderStatus::Pending, $order->status);
        $this->assertStringContainsString('Producto manual', $order->erp_error);
    }

    public function test_missing_quotation_settings_keep_the_order_pending_without_calling_the_erp(): void
    {
        config(['services.erp.quotation.customer_id' => null]);
        $this->fakeErp();
        $product = Product::factory()->create(['external_id' => '14', 'stock' => 6]);
        app(Cart::class)->add($product->id, 1);

        $this->placeOrder();

        Http::assertNothingSent();
        $order = Order::firstOrFail();
        $this->assertSame(OrderStatus::Pending, $order->status);
        $this->assertStringContainsString('ERP_CUSTOMER_ID', $order->erp_error);
    }

    public function test_admin_can_resend_a_pending_order_to_the_erp(): void
    {
        $this->actingAsSuperAdmin();
        $order = $this->pendingOrder(erpError: 'ERP request login could not connect');
        $this->fakeErp();

        Livewire::test(ViewOrder::class, ['record' => $order->getRouteKey()])
            ->assertActionVisible('sendToErp')
            ->callAction('sendToErp')
            ->assertNotified('Pedido enviado al ERP');

        $order->refresh();
        $this->assertSame(OrderStatus::SentToErp, $order->status);
        $this->assertSame('CT01-00000002', $order->erp_quotation_number);
        $this->assertNull($order->erp_error);
    }

    public function test_the_quotation_id_is_saved_while_the_order_lock_is_still_held(): void
    {
        $order = $this->pendingOrder();
        $this->fakeErp();
        $lockFreeWhenSaved = null;
        Order::saving(function (Order $saving) use (&$lockFreeWhenSaved): void {
            if ($saving->isDirty('erp_quotation_id') && $saving->erp_quotation_id !== null) {
                $probe = Cache::lock("erp-quotation-{$saving->id}", 5);
                $lockFreeWhenSaved = $probe->get();
                $lockFreeWhenSaved && $probe->release();
            }
        });

        $this->assertSame(QuotationResult::Sent, app(QuotationSender::class)->send($order));

        $this->assertFalse($lockFreeWhenSaved);
        $this->assertSame(2, $order->fresh()->erp_quotation_id);
    }

    public function test_a_send_already_in_progress_is_reported_as_busy_and_changes_nothing(): void
    {
        $this->actingAsSuperAdmin();
        $order = $this->pendingOrder(erpError: 'old failure');
        $this->fakeErp();
        $lock = Cache::lock("erp-quotation-{$order->id}", 60);
        $lock->get();

        $this->assertSame(QuotationResult::Busy, app(QuotationSender::class)->send($order));
        Livewire::test(ViewOrder::class, ['record' => $order->getRouteKey()])
            ->callAction('sendToErp')
            ->assertNotified('El pedido ya se está enviando al ERP');

        Http::assertNothingSent();
        $this->assertSame('old failure', $order->fresh()->erp_error);
        $lock->release();
    }

    public function test_a_successful_send_keeps_a_status_the_admin_already_moved_forward(): void
    {
        $order = $this->pendingOrder();
        $order->forceFill(['status' => OrderStatus::Confirmed])->save();
        $this->fakeErp();

        $this->assertSame(QuotationResult::Sent, app(QuotationSender::class)->send($order->fresh()));

        $order->refresh();
        $this->assertSame(OrderStatus::Confirmed, $order->status);
        $this->assertSame(2, $order->erp_quotation_id);
    }

    public function test_the_order_lock_outlasts_a_slow_send_with_a_long_erp_timeout(): void
    {
        config(['services.erp.timeout' => 60]);
        $order = $this->pendingOrder();
        $lockFreeDuringPost = null;
        $this->freshHttp();
        Http::fake([
            self::BASE.'/auth/login' => Http::response(['status' => 'success', 'token' => '1|test-token']),
            self::BASE.'/cotizaciones' => function () use ($order, &$lockFreeDuringPost) {
                $this->travel(170)->seconds();
                $probe = Cache::lock("erp-quotation-{$order->id}", 5);
                $lockFreeDuringPost = $probe->get();
                $lockFreeDuringPost && $probe->release();

                return Http::response(['status' => 'success', 'data' => ['idcotizacion' => 2, 'numero' => 'CT01-00000002']]);
            },
        ]);

        app(QuotationSender::class)->send($order);

        $this->assertFalse($lockFreeDuringPost);
    }

    public function test_a_cache_outage_keeps_the_order_pending_without_throwing(): void
    {
        $order = $this->pendingOrder();
        $this->fakeErp();
        Cache::shouldReceive('lock')->andThrow(new \RuntimeException('cache store unavailable'));

        $this->assertSame(QuotationResult::Failed, app(QuotationSender::class)->send($order));

        Http::assertNothingSent();
        $order->refresh();
        $this->assertSame(OrderStatus::Pending, $order->status);
        $this->assertStringContainsString('cache store unavailable', $order->erp_error);
    }

    public function test_a_failed_save_after_the_erp_created_the_quotation_is_reported_clearly(): void
    {
        $order = $this->pendingOrder();
        $this->fakeErp();
        $failOnce = true;
        Order::saving(function (Order $saving) use (&$failOnce): void {
            if ($failOnce && $saving->isDirty('erp_quotation_id') && $saving->erp_quotation_id !== null) {
                $failOnce = false;
                throw new \RuntimeException('database went away');
            }
        });

        $this->assertSame(QuotationResult::Failed, app(QuotationSender::class)->send($order));

        $order->refresh();
        $this->assertSame(OrderStatus::Pending, $order->status);
        $this->assertNull($order->erp_quotation_id);
        $this->assertNull($order->erp_sent_at);
        $this->assertStringContainsString('ERP created quotation 2', $order->erp_error);
        $this->assertStringContainsString('database went away', $order->erp_error);
    }

    public function test_change_status_requires_permission_to_update_orders(): void
    {
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\ShieldSeeder']);
        $role = Role::findOrCreate('order_viewer', 'web');
        $role->givePermissionTo([Permission::findOrCreate('ViewAny:Order', 'web'), Permission::findOrCreate('View:Order', 'web')]);
        $user = User::factory()->create();
        $user->assignRole($role);
        $this->actingAs($user);
        $order = $this->pendingOrder();

        Livewire::test(ViewOrder::class, ['record' => $order->getRouteKey()])
            ->assertActionHidden('editStatus');
    }

    public function test_send_to_erp_requires_permission_to_update_orders(): void
    {
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\ShieldSeeder']);
        $role = Role::findOrCreate('order_viewer', 'web');
        $role->givePermissionTo([Permission::findOrCreate('ViewAny:Order', 'web'), Permission::findOrCreate('View:Order', 'web')]);
        $user = User::factory()->create();
        $user->assignRole($role);
        $this->actingAs($user);
        $order = $this->pendingOrder();
        $this->fakeErp();

        Livewire::test(ViewOrder::class, ['record' => $order->getRouteKey()])
            ->assertActionHidden('sendToErp');

        Http::assertNothingSent();
    }

    public function test_admin_resend_failure_is_reported_and_the_order_stays_pending(): void
    {
        $this->actingAsSuperAdmin();
        $order = $this->pendingOrder();
        $this->fakeErp(quotation: Http::response(['status' => 'error'], 422));

        Livewire::test(ViewOrder::class, ['record' => $order->getRouteKey()])
            ->callAction('sendToErp')
            ->assertNotified('No se pudo enviar el pedido al ERP');

        $this->assertSame(OrderStatus::Pending, $order->fresh()->status);
    }

    public function test_an_order_already_sent_is_never_sent_again(): void
    {
        $this->actingAsSuperAdmin();
        $order = $this->pendingOrder();
        $order->forceFill(['status' => OrderStatus::SentToErp, 'erp_quotation_id' => 2, 'erp_quotation_number' => 'CT01-00000002'])->save();
        $this->fakeErp();

        Livewire::test(ViewOrder::class, ['record' => $order->getRouteKey()])
            ->assertActionHidden('sendToErp');
        app(QuotationSender::class)->send($order->fresh());

        Http::assertNothingSent();
    }

    public function test_admin_status_change_still_works_and_lists_the_sent_status(): void
    {
        $this->actingAsSuperAdmin();
        $order = $this->pendingOrder();

        Livewire::test(ViewOrder::class, ['record' => $order->getRouteKey()])
            ->callAction('editStatus', ['status' => OrderStatus::Confirmed->value]);

        $this->assertSame(OrderStatus::Confirmed, $order->fresh()->status);
        $this->assertSame('Enviado al ERP', OrderStatus::SentToErp->getLabel());
    }

    /**
     * Deferred callbacks run once the response is sent; tests run them right after the submit.
     */
    private function placeOrder(bool $runDeferred = true): Testable
    {
        $component = Livewire::test('checkout.order-form')
            ->set('first_name', 'Ana')
            ->set('last_name', 'Pérez')
            ->set('phone', '912345678')
            ->set('delivery_method', 'pickup')
            ->set('payment_preference', 'bank_transfer')
            ->set('notes', 'Llamar al llegar')
            ->set('privacy_accepted', true)
            ->call('submit');

        if ($runDeferred) {
            app(DeferredCallbackCollection::class)->invoke();
        }

        return $component;
    }

    private function pendingOrder(?string $erpError = null): Order
    {
        $product = Product::factory()->create(['external_id' => '14', 'price' => 18]);
        $order = Order::factory()->create(['status' => OrderStatus::Pending]);
        $order->items()->create(['product_id' => $product->id, 'product_name' => $product->name, 'unit_price' => 18, 'quantity' => 1, 'line_total' => 18]);
        $order->forceFill(['erp_error' => $erpError])->save();

        return $order->fresh();
    }

    private function actingAsSuperAdmin(): void
    {
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\ShieldSeeder']);
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super_admin', 'web'));
        $this->actingAs($user);
    }

    private function fakeErp(mixed $quotation = null): void
    {
        $this->freshHttp();
        Http::fake([
            self::BASE.'/auth/login' => Http::response(['status' => 'success', 'token' => '1|test-token']),
            self::BASE.'/cotizaciones' => $quotation ?? Http::response([
                'status' => 'success',
                'message' => 'Cotización registrada: CT01-00000002, sin reserva de stock.',
                'data' => ['idcotizacion' => 2, 'numero' => 'CT01-00000002', 'total' => '39.50'],
            ]),
        ]);
    }

    private function freshHttp(): void
    {
        Http::swap(new HttpFactory);
        Http::preventStrayRequests();
    }
}
