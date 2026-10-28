<?php

namespace App\Http\Controllers\Store;

use App\Enums\BannerPlacement;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\CompanySetting;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $visible = fn () => Product::query()->visibleInStore()->with(['category', 'brand']);
        $featured = $visible()->where('is_featured', true)->latest()->take(8)->get();
        $latest = $visible()->latest()->take(8)->get();
        $bestSellingIds = OrderItem::query()
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->where('products.is_active', true)->where('products.stock', '>', 0)
            ->selectRaw('order_items.product_id, SUM(order_items.quantity) AS units_sold')
            ->groupBy('order_items.product_id')->orderByDesc('units_sold')->limit(6)->pluck('product_id');
        $bestSellers = $bestSellingIds->isEmpty()
            ? $visible()->latest()->take(6)->get()
            : $visible()->whereIn('id', $bestSellingIds)->orderByRaw('CASE id '.implode(' ', $bestSellingIds->values()->map(fn ($id, $i) => "WHEN {$id} THEN {$i}")->all()).' END')->get();

        return view('store.home', [
            'title' => 'Inicio',
            'description' => 'Encuentra productos de tecnología, cómputo y oficina en Mastersoft.',
            'heroBanners' => Banner::query()->where('placement', BannerPlacement::Hero->value)->where('is_active', true)->orderBy('sort_order')->get(),
            'sideBanner' => Banner::query()->where('placement', BannerPlacement::Side->value)->where('is_active', true)->orderBy('sort_order')->first(),
            'categories' => Category::query()->where('is_active', true)->whereHas('products', fn (Builder $q) => $q->visibleInStore())->withCount(['products as visible_products_count' => fn (Builder $q) => $q->visibleInStore()])->orderBy('sort_order')->orderBy('name')->get(),
            'promotions' => Promotion::query()->where('is_active', true)->where(fn (Builder $q) => $q->whereNull('product_id')->orWhereHas('product', fn (Builder $p) => $p->visibleInStore()))->with('product')->orderBy('sort_order')->take(2)->get(),
            'featuredProducts' => $featured,
            'latestProducts' => $latest,
            'bestSellers' => $bestSellers,
            'company' => CompanySetting::current(),
        ]);
    }
}
