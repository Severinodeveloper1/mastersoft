<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Contracts\View\View;

/**
 * Placeholder until task T4 of odd/tasks/storefront-rebuild.md implements this page.
 */
class ProductController extends Controller
{
    public function __invoke(Product $product): View
    {
        abort_unless(Product::query()->visibleInStore()->whereKey($product->getKey())->exists(), 404);

        return view('store.placeholder', ['title' => $product->name]);
    }
}
