<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use Illuminate\Contracts\View\View;

class CatalogController extends Controller
{
    public function __invoke(): View
    {
        return view('store.catalog.index', [
            'title' => 'Tienda',
            'description' => 'Explora el catálogo de productos de Mastersoft.',
            'company' => CompanySetting::current(),
        ]);
    }
}
