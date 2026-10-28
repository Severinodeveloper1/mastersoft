<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

/**
 * Placeholder until task T4 of odd/tasks/storefront-rebuild.md implements this page.
 */
class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('store.placeholder', ['title' => 'Inicio']);
    }
}
