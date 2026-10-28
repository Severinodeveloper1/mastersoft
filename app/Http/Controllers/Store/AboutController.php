<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\AboutPage;
use App\Models\Brand;
use App\Models\CompanySetting;
use Illuminate\Contracts\View\View;

class AboutController extends Controller
{
    public function __invoke(): View
    {
        return view('store.about.index', [
            'about' => AboutPage::current()->load('media'),
            'brands' => Brand::query()->where('is_active', true)->whereHas('media', fn ($query) => $query->where('collection_name', 'logo'))->with('media')->orderBy('name')->get(),
            'company' => CompanySetting::current(),
        ]);
    }
}
