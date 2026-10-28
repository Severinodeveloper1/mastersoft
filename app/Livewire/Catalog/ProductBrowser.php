<?php

namespace App\Livewire\Catalog;

use App\Models\Brand;
use App\Models\Category;
use App\Models\CompanySetting;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProductBrowser extends Component
{
    use WithPagination;

    #[Url(as: 'buscar', except: '')]
    public $buscar = '';

    #[Url(as: 'categoria', except: '')]
    public $categoria = '';

    /** @var array<int, string> */
    #[Url(as: 'marcas', except: [])]
    public $marcas = [];

    #[Url(as: 'precio_min', except: '')]
    public $precio_min = '';

    #[Url(as: 'precio_max', except: '')]
    public $precio_max = '';

    #[Url(as: 'orden', except: 'relevancia')]
    public $orden = 'relevancia';

    #[Url(as: 'vista', except: 'grid')]
    public $vista = 'grid';

    public bool $filtersOpen = false;

    public int $perPage = 12;

    public function updated(string $property): void
    {
        if ($property !== 'filtersOpen' && $property !== 'perPage') {
            $this->resetPage();
        }
    }

    public function clearFilters(): void
    {
        $this->reset(['buscar', 'categoria', 'marcas', 'precio_min', 'precio_max', 'orden', 'vista']);
        $this->resetPage();
    }

    public function loadMore(): void
    {
        $this->perPage += 12;
    }

    public function render()
    {
        $categories = Category::query()->where('is_active', true)
            ->whereHas('products', fn (Builder $q) => $q->visibleInStore())
            ->withCount(['products as visible_products_count' => fn (Builder $q) => $q->visibleInStore()])
            ->orderBy('sort_order')->orderBy('name')->get();
        $brands = Brand::query()->where('is_active', true)
            ->whereHas('products', fn (Builder $q) => $q->visibleInStore())
            ->withCount(['products as visible_products_count' => fn (Builder $q) => $q->visibleInStore()])
            ->orderBy('name')->get();

        $query = Product::query()->visibleInStore()->with(['category', 'brand']);
        $term = is_string($this->buscar) ? trim($this->buscar) : '';
        if ($term !== '') {
            $query->where(fn (Builder $q) => $q->where('name', 'like', "%{$term}%")
                ->orWhereHas('brand', fn (Builder $brand) => $brand->where('name', 'like', "%{$term}%")));
        }

        $categorySlug = is_string($this->categoria) ? $this->categoria : '';
        if ($categorySlug !== '' && $categories->contains('slug', $categorySlug)) {
            $query->whereHas('category', fn (Builder $q) => $q->where('slug', $categorySlug)->where('is_active', true));
        }

        $selectedBrands = is_array($this->marcas) ? array_filter($this->marcas, 'is_string') : [];
        $validBrandSlugs = $brands->whereIn('slug', $selectedBrands)->pluck('slug')->all();
        if ($validBrandSlugs !== []) {
            $query->whereHas('brand', fn (Builder $q) => $q->whereIn('slug', $validBrandSlugs)->where('is_active', true));
        }

        $minimum = is_numeric($this->precio_min) && (float) $this->precio_min >= 0 ? (float) $this->precio_min : null;
        $maximum = is_numeric($this->precio_max) && (float) $this->precio_max >= 0 ? (float) $this->precio_max : null;
        if ($minimum !== null) {
            $query->where('price', '>=', $minimum);
        }
        if ($maximum !== null) {
            $query->where('price', '<=', $maximum);
        }

        match ($this->orden) {
            'menor-precio' => $query->orderBy('price')->orderBy('name'),
            'mayor-precio' => $query->orderByDesc('price')->orderBy('name'),
            'nombre' => $query->orderBy('name'),
            default => $query->latest(),
        };

        return view('livewire.catalog.product-browser', [
            'products' => $query->paginate($this->perPage),
            'categories' => $categories,
            'brands' => $brands,
            'company' => CompanySetting::current(),
        ]);
    }
}
