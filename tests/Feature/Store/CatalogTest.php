<?php

namespace Tests\Feature\Store;

use App\Livewire\Catalog\ProductBrowser;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_filters_search_category_brands_price_and_sort(): void
    {
        $category = Category::factory()->create(['slug' => 'redes']);
        $otherCategory = Category::factory()->create(['slug' => 'audio']);
        $brand = Brand::factory()->create(['name' => 'Acme', 'slug' => 'acme']);
        $otherBrand = Brand::factory()->create(['name' => 'Nova', 'slug' => 'nova']);
        Product::factory()->for($category)->for($brand)->create(['name' => 'Router Uno', 'price' => 50]);
        Product::factory()->for($category)->for($otherBrand)->create(['name' => 'Router Dos', 'price' => 100]);
        Product::factory()->for($otherCategory)->for($brand)->create(['name' => 'Parlante', 'price' => 20]);

        Livewire::test(ProductBrowser::class)->set('buscar', 'Router')->assertSee('Router Uno')->assertSee('Router Dos')->assertDontSee('Parlante')
            ->set('categoria', 'redes')->set('marcas', ['acme', 'nova'])->assertSee('Router Uno')->assertSee('Router Dos')
            ->set('precio_min', '60')->assertDontSee('Router Uno')->assertSee('Router Dos')
            ->set('precio_max', '90')->assertDontSee('Router Dos');
    }

    public function test_catalog_sort_invalid_values_and_empty_search_are_safe(): void
    {
        Product::factory()->create(['name' => 'Alfa', 'price' => 20]);
        Product::factory()->create(['name' => 'Beta', 'price' => 10]);
        Livewire::test(ProductBrowser::class)->set('orden', 'menor-precio')->assertSeeInOrder(['Beta', 'Alfa'])
            ->set('orden', 'not-valid')->set('vista', 'not-valid')->assertOk()
            ->set('buscar', 'no existe')->assertSee('No encontramos productos para')->assertSee('no existe');
    }

    public function test_catalog_supports_every_sort_and_ignores_invalid_filter_values(): void
    {
        Product::factory()->create(['name' => 'Alfa', 'price' => 20]);
        Product::factory()->create(['name' => 'Beta', 'price' => 10]);
        Product::factory()->create(['name' => 'Gamma', 'price' => 30]);

        Livewire::test(ProductBrowser::class)->set('orden', 'mayor-precio')->assertSeeInOrder(['Gamma', 'Alfa', 'Beta'])
            ->set('orden', 'nombre')->assertSeeInOrder(['Alfa', 'Beta', 'Gamma'])
            ->set('orden', 'relevancia')->assertOk()
            ->set('categoria', 'missing-category')->set('marcas', ['missing-brand'])->set('precio_min', 'invalid')
            ->set('precio_max', '-5')->set('vista', 'table')->assertSee('Alfa')->assertSee('Beta')->assertSee('Gamma');
    }

    public function test_catalog_url_query_keys_initialize_livewire_filters(): void
    {
        Product::factory()->create(['name' => 'Router buscado']);
        Product::factory()->create(['name' => 'Parlante ajeno']);

        $this->get(route('store.index', ['buscar' => 'Router']))->assertOk()->assertSee('Router buscado')->assertDontSee('Parlante ajeno');
    }
}
