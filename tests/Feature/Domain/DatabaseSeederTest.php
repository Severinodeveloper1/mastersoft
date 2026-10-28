<?php

namespace Tests\Feature\Domain;

use App\Models\CompanySetting;
use App\Models\LegalPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_seeder_creates_required_site_rows(): void
    {
        $this->seed();

        $this->assertSame(1, CompanySetting::count());
        $this->assertSame(2, LegalPage::count());
    }
}
