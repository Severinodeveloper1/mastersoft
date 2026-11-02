<?php

namespace Tests\Feature\Store;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HomeCategoriesMigrationTest extends TestCase
{
    use RefreshDatabase;

    private const MIGRATION = 'database/migrations/2026_11_02_000001_add_show_on_home_to_categories_table.php';

    public function test_the_migration_marks_the_six_categories_with_the_most_visible_products(): void
    {
        $categories = collect(range(1, 8))->map(fn (int $i) => Category::factory()->create(['name' => "Categoria $i"]));
        // Category i gets i visible products, so categories 3..8 are the six largest.
        $categories->each(fn (Category $category, int $index) => Product::factory()->count($index + 1)->create(['category_id' => $category->id]));
        // Hidden or out-of-stock products do not count: they would push category 1 to the top otherwise.
        Product::factory()->count(10)->create(['category_id' => $categories[0]->id, 'is_active' => false]);
        Product::factory()->count(10)->create(['category_id' => $categories[0]->id, 'stock' => 0]);
        // An inactive category is never chosen, however many products it has.
        $inactive = Category::factory()->create(['is_active' => false]);
        Product::factory()->count(20)->create(['category_id' => $inactive->id]);

        $this->artisan('migrate:rollback', ['--path' => self::MIGRATION])->assertSuccessful();
        $this->artisan('migrate', ['--path' => self::MIGRATION])->assertSuccessful();

        $marked = DB::table('categories')->where('show_on_home', true)->orderBy('id')->pluck('name')->all();
        $this->assertSame(['Categoria 3', 'Categoria 4', 'Categoria 5', 'Categoria 6', 'Categoria 7', 'Categoria 8'], $marked);
    }
}
