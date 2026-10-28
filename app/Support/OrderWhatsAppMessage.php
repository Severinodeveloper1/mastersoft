<?php

namespace App\Support;

use App\Enums\DeliveryMethod;
use App\Models\Order;

final class OrderWhatsAppMessage
{
    public static function make(Order $order): string
    {
        $order->loadMissing('items');
        $lines = [
            "Hola Mastersoft, acabo de registrar el pedido {$order->code}",
            "Cliente: {$order->customerName()}",
            'Productos:',
        ];

        foreach ($order->items as $item) {
            $lines[] = "{$item->quantity} x {$item->product_name} - ".Money::format($item->unit_price);
        }

        $lines[] = 'Total: '.Money::format($order->total);
        $lines[] = 'Entrega: '.$order->delivery_method->getLabel();

        if ($order->delivery_method === DeliveryMethod::Shipping) {
            $lines[] = 'Lugar: '.implode(', ', array_filter([
                $order->address,
                $order->district,
                $order->province,
                $order->department,
            ]));
            if ($order->reference) {
                $lines[] = 'Referencia: '.$order->reference;
            }
        }

        $lines[] = 'Forma de pago preferida: '.$order->payment_preference->getLabel();
        if ($order->notes) {
            $lines[] = 'Notas: '.$order->notes;
        }

        return implode("\n", $lines);
    }
}
