<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

/**
 * Placeholder until task T6 of odd/tasks/storefront-rebuild.md implements this page.
 * The route passes the LegalPage slug (LegalPage::PRIVACY or LegalPage::TERMS).
 */
class LegalPageController extends Controller
{
    public function __invoke(string $slug): View
    {
        return view('store.placeholder', ['title' => $slug === 'terminos-y-condiciones' ? 'Términos y condiciones' : 'Políticas de privacidad']);
    }
}
