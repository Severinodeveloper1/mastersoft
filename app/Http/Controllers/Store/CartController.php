<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Placeholder until task T5 of odd/tasks/storefront-rebuild.md implements the cart.
 */
class CartController extends Controller
{
    public function show(): View
    {
        return view('store.placeholder', ['title' => 'Carrito de compras']);
    }

    public function add(): RedirectResponse
    {
        return back();
    }
}
