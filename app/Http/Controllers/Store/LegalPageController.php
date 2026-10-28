<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\LegalPage;
use Illuminate\Contracts\View\View;

class LegalPageController extends Controller
{
    public function __invoke(string $slug): View
    {
        $page = LegalPage::query()->where('slug', $slug)->firstOrFail();

        return view('store.legal.show', compact('page'));
    }
}
