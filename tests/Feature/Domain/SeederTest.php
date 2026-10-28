<?php

namespace Tests\Feature\Domain;

use App\Models\LegalPage;
use App\Models\Product;
use Database\Seeders\ContentSeeder;
use Database\Seeders\DemoCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_content_seeder_creates_both_legal_pages_once(): void
    {
        $this->seed(ContentSeeder::class);
        $this->seed(ContentSeeder::class);

        $this->assertEqualsCanonicalizing(
            [LegalPage::PRIVACY, LegalPage::TERMS],
            LegalPage::pluck('slug')->all(),
        );
    }

    public function test_demo_catalog_seeder_is_idempotent_and_has_real_offers(): void
    {
        $this->seed(DemoCatalogSeeder::class);
        $this->seed(DemoCatalogSeeder::class);

        $this->assertSame(18, Product::count());
        $this->assertSame(5, Product::all()->filter->isOnSale()->count());
    }
}
