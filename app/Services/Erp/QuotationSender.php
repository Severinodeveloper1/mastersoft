<?php

namespace App\Services\Erp;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Registers a store order in the ERP as a quotation.
 *
 * It never throws: the customer always gets the order confirmation. On success the order is marked
 * as sent; on any failure it stays pending with the reason in `erp_error`, so the admin can resend it.
 */
class QuotationSender
{
    public function __construct(private readonly ErpClient $client) {}

    public function send(Order $order): QuotationResult
    {
        if ($order->erp_quotation_id !== null) {
            return QuotationResult::Sent;
        }

        try {
            // A double click on "resend" must not create two quotations for one order.
            $lock = Cache::lock("erp-quotation-{$order->id}", $this->lockSeconds());
            if (! $lock->get()) {
                return QuotationResult::Busy;
            }
        } catch (Throwable $exception) {
            return $this->fail($order, $exception->getMessage());
        }

        $quotation = null;

        try {
            $order->refresh();
            if ($order->erp_quotation_id !== null) {
                return QuotationResult::Sent;
            }

            $payload = $this->payload($order);
            $this->client->login();
            $quotation = $this->client->create('/cotizaciones', $payload);

            // Without the id the order would look sent yet stay resendable, risking a duplicate.
            if (! is_numeric($quotation['idcotizacion'] ?? null)) {
                throw new ErpException('ERP answered success but returned no quotation id; check the ERP before resending.');
            }

            // Saved before the lock is released, so a racing resend always sees the order as sent.
            $order->forceFill([
                // An admin may already have moved the order forward; only a pending order changes status.
                'status' => $order->status === OrderStatus::Pending ? OrderStatus::SentToErp : $order->status,
                'erp_quotation_id' => (int) $quotation['idcotizacion'],
                'erp_quotation_number' => $quotation['numero'] ?? null,
                'erp_sent_at' => now(),
                'erp_error' => null,
            ])->save();
        } catch (Throwable $exception) {
            // Drop the unsaved success fields so only the error is written and the order stays pending.
            $order->discardChanges();

            return $this->fail($order, is_numeric($quotation['idcotizacion'] ?? null)
                ? "ERP created quotation {$quotation['idcotizacion']} but the order could not be updated; do not resend: {$exception->getMessage()}"
                : $exception->getMessage());
        } finally {
            rescue(fn () => $lock->release(), report: false);
        }

        Log::info('ERP quotation created.', ['order' => $order->code, 'quotation' => $order->erp_quotation_number]);

        return QuotationResult::Sent;
    }

    /**
     * Login may try twice with a pause, then the POST runs once, each up to the configured timeout;
     * the lock must outlive that whole send or a resend could race it.
     */
    private function lockSeconds(): int
    {
        return 3 * (int) config('services.erp.timeout', 30) + 60;
    }

    private function fail(Order $order, string $error): QuotationResult
    {
        Log::error('ERP quotation failed; the order stays pending.', ['order' => $order->code, 'error' => $error]);
        // The order itself is already saved; failing to note the error must not break the caller.
        rescue(fn () => $order->forceFill(['erp_error' => Str::limit($error, 1000)])->save(), report: false);

        return QuotationResult::Failed;
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Order $order): array
    {
        $settings = config('services.erp.quotation', []);
        foreach (['customer_id' => 'ERP_CUSTOMER_ID', 'currency_id' => 'ERP_CURRENCY_ID', 'payment_condition_id' => 'ERP_PAYMENT_CONDITION_ID'] as $key => $env) {
            if (blank($settings[$key] ?? null)) {
                throw new ErpException("ERP quotation settings are not configured ({$env}).");
            }
        }

        $items = $order->items()->with('product')->get()->map(function ($item): array {
            $externalId = $item->product?->external_id;
            if (blank($externalId)) {
                throw new ErpException("Product \"{$item->product_name}\" does not come from the ERP, so it cannot be sent.");
            }

            return [
                'idproducto' => (int) $externalId,
                'cantidad' => $item->quantity,
                'preciounitario' => (float) $item->unit_price,
            ];
        })->all();

        $observation = collect([
            'DESDE TIENDA WEB',
            $order->code,
            $order->customerName(),
            "Cel. {$order->phone}",
            $order->notes,
        ])->filter(fn ($part) => filled($part))->implode(' - ');

        return [
            'idcliente' => (int) $settings['customer_id'],
            'idmoneda' => (int) $settings['currency_id'],
            'idcondicionpago' => (int) $settings['payment_condition_id'],
            'dias' => (int) $settings['days'],
            'reservarstock' => false,
            'observacion' => Str::limit($observation, 500),
            'items' => $items,
        ];
    }
}
