<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use Illuminate\Contracts\View\View;

class ContactController extends Controller
{
    public function __invoke(): View
    {
        $company = CompanySetting::current();
        $mapUrl = $company->map_embed_url;
        $mapUrl = is_string($mapUrl) && preg_match('~^https://www\.google\.com/maps/embed(?:\?|/|$)~', $mapUrl) ? $mapUrl : null;

        return view('store.contact.index', compact('company', 'mapUrl'));
    }
}
