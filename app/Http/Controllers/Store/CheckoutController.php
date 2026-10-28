<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\Order;
use App\Services\Cart;
use App\Support\OrderWhatsAppMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\URL;

class CheckoutController extends Controller
{
    public function create(Cart $cart): View|RedirectResponse
    {
        if ($cart->unitCount() === 0) {
            return redirect()->route('cart.show');
        }

        return view('store.checkout.create');
    }

    public function confirmation(Order $order): View
    {
        $order->load('items');
        $company = CompanySetting::current();

        return view('store.checkout.confirmation', [
            'order' => $order,
            'company' => $company,
            'whatsappUrl' => $company->whatsappUrl(OrderWhatsAppMessage::make($order)),
            'confirmationUrl' => URL::signedRoute('checkout.confirmation', ['order' => $order->code]),
        ]);
    }
}
