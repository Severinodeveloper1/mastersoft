<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\CompanySettings;
use App\Filament\Resources\Claims\ClaimResource;
use App\Filament\Resources\Claims\Pages\EditClaim;
use App\Filament\Resources\Claims\Pages\ListClaims;
use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Claim;
use App\Models\CompanySetting;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FilamentAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\ShieldSeeder']);
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super_admin', 'web'));
        $this->actingAs($user);
    }

    public function test_product_list_and_sale_filter_show_only_products_with_higher_regular_price(): void
    {
        $sale = Product::factory()->create(['price' => 80, 'regular_price' => 100]);
        Product::factory()->create(['price' => 100, 'regular_price' => null]);
        Product::factory()->create(['price' => 100, 'regular_price' => 90]);

        Livewire::test(ListProducts::class)
            ->assertCanSeeTableRecords([$sale])
            ->filterTable('sale')
            ->assertCanSeeTableRecords([$sale])
            ->assertCanNotSeeTableRecords(Product::query()->whereKeyNot($sale->getKey())->get());
    }

    public function test_orders_have_no_create_page(): void
    {
        $this->assertArrayNotHasKey('create', OrderResource::getPages());
    }

    public function test_product_creation_persists_sale_prices(): void
    {
        $category = Category::factory()->create();
        $brand = Brand::factory()->create();

        Livewire::test(CreateProduct::class)
            ->fillForm([
                'category_id' => $category->id, 'brand_id' => $brand->id, 'name' => 'Equipo de prueba', 'slug' => 'equipo-prueba',
                'summary' => 'Resumen', 'description' => '<p>Descripción</p>', 'information' => '<p>Información</p>',
                'price' => 80, 'regular_price' => 100, 'stock' => 5, 'unit' => 'unidad', 'is_active' => true, 'is_featured' => false,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('products', ['slug' => 'equipo-prueba', 'price' => '80.00', 'regular_price' => '100.00']);
    }

    public function test_erp_product_without_brand_can_be_edited_in_admin(): void
    {
        $product = Product::factory()->create(['external_id' => '14', 'brand_id' => null]);

        Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()])
            ->fillForm(['summary' => 'Resumen del admin'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Resumen del admin', $product->fresh()->summary);
        $this->assertNull($product->fresh()->brand_id);
    }

    public function test_product_images_uploaded_in_admin_are_stored_on_the_public_disk(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        Livewire::test(CreateProduct::class)
            ->fillForm([
                'category_id' => Category::factory()->create()->id, 'brand_id' => Brand::factory()->create()->id,
                'name' => 'Equipo con foto', 'slug' => 'equipo-con-foto', 'price' => 80, 'stock' => 5, 'unit' => 'unidad', 'is_active' => true,
                'images' => [UploadedFile::fake()->image('foto.jpg')],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $media = Product::where('slug', 'equipo-con-foto')->firstOrFail()->getFirstMedia('images');
        $this->assertSame('public', $media->disk);
        Storage::disk('public')->assertExists($media->getPathRelativeToRoot());
        $this->assertStringContainsString('/storage/', $media->getUrl());
    }

    public function test_claim_edit_locks_consumer_fields_and_sets_response_date(): void
    {
        $claim = Claim::factory()->create(['first_name' => 'Original']);

        Livewire::test(EditClaim::class, ['record' => $claim->getRouteKey()])
            ->assertFormFieldDoesNotExist('first_name')
            ->fillForm(['status' => 'answered', 'provider_response' => 'Respuesta formal', 'responded_at' => null])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('claims', ['id' => $claim->id, 'first_name' => 'Original', 'provider_response' => 'Respuesta formal']);
        $this->assertNotNull($claim->fresh()->responded_at);
    }

    public function test_claim_table_displays_business_days_or_overdue_label(): void
    {
        $due = Claim::factory()->create(['response_due_on' => today()->addWeekdays(2)]);
        $overdue = Claim::factory()->create(['response_due_on' => today()->subDay()]);

        Livewire::test(ListClaims::class)
            ->assertCanSeeTableRecords([$due, $overdue])
            ->assertSee('2')
            ->assertSee('Vencido');
    }

    public function test_company_settings_page_saves_current_settings(): void
    {
        Livewire::test(CompanySettings::class)
            ->callAction('edit', data: ['name' => 'Mastersoft', 'whatsapp_number' => '51942123456'])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('company_settings', ['id' => CompanySetting::current()->id, 'name' => 'Mastersoft', 'whatsapp_number' => '51942123456']);
        $this->assertArrayHasKey('view', ClaimResource::getPages());
    }
}
