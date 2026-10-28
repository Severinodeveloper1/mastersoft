<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Cart;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function show(): View
    {
        return view('store.cart.index');
    }

    public function add(Request $request, Cart $cart): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);
        $product = Product::visibleInStore()->find($data['product_id']);

        if (! $product) {
            return back()->withErrors(['cart' => 'Este producto ya no está disponible.']);
        }

        $cart->add($product->id, $data['quantity']);
        $request->session()->flash('cart_drawer_open', true);

        return back()->with('status', 'Producto agregado al carrito');
    }
}
