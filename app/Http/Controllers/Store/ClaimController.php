<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use Illuminate\Contracts\View\View;

class ClaimController extends Controller
{
    public function __invoke(): View
    {
        return view('store.claims.create', ['company' => CompanySetting::current()]);
    }
}
