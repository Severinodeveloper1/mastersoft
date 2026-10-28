<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;

/**
 * Placeholder until task T5 of odd/tasks/storefront-rebuild.md implements the order flow.
 */
class CheckoutController extends Controller
{
    public function create(): View
    {
        return view('store.placeholder', ['title' => 'Enviar pedido por WhatsApp']);
    }

    public function confirmation(Order $order): View
    {
        return view('store.placeholder', ['title' => '¡Pedido registrado!']);
    }
}
