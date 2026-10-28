<?php

namespace App\View\Composers;

use App\Models\Category;
use App\Models\CompanySetting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;

/**
 * Shares the company data and the category menu with the storefront shell.
 * Bound as scoped, so the queries run once per request even across partials.
 */
class StoreLayoutComposer
{
    private ?CompanySetting $company = null;

    /** @var Collection<int, Category>|null */
    private ?Collection $menuCategories = null;

    public function compose(View $view): void
    {
        $this->company ??= CompanySetting::current();
        $this->menuCategories ??= Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $view->with([
            'company' => $this->company,
            'menuCategories' => $this->menuCategories,
        ]);
    }
}
