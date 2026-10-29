<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\Product;
use Illuminate\Contracts\View\View;

class ProductController extends Controller
{
    public function __invoke(Product $product): View
    {
        abort_unless(Product::query()->visibleInStore()->whereKey($product->getKey())->exists(), 404);

        $product->load(['category', 'brand']);
        $images = array_map(fn (string $url) => ['url' => $url, 'alt' => $product->name], $product->imageUrls());
        if ($images === []) {
            $images[] = ['url' => '', 'alt' => $product->name];
        }

        return view('store.product.show', [
            'title' => $product->name,
            'description' => $product->summary ?: "Compra {$product->name} en Mastersoft.",
            'product' => $product,
            'images' => $images,
            'relatedProducts' => $product->category
                ? Product::query()->visibleInStore()->with('category')->where('category_id', $product->category_id)->whereKeyNot($product->getKey())->latest()->take(4)->get()
                : collect(),
            'company' => CompanySetting::current(),
        ]);
    }
}
