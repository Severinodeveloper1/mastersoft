<?php

namespace Tests\Feature\Domain;

use App\Models\AboutPage;
use App\Models\CompanySetting;
use App\Models\LegalPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SingletonSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_settings_and_about_page_are_single_rows(): void
    {
        $settings = CompanySetting::current();
        $settings->update(['phone' => '+51 942 000 000']);

        $this->assertSame(1, CompanySetting::count());
        $this->assertSame('+51 942 000 000', CompanySetting::current()->phone);
        $this->assertSame('TROYA COMPANY E.I.R.L.', $settings->legal_name);
        $this->assertSame('20614007584', $settings->ruc);

        AboutPage::current();
        AboutPage::current();
        $this->assertSame(1, AboutPage::count());
    }

    public function test_legal_pages_are_found_by_slug(): void
    {
        LegalPage::factory()->create(['slug' => 'politicas-de-privacidad', 'title' => 'Políticas de privacidad']);

        $this->assertSame('Políticas de privacidad', LegalPage::where('slug', 'politicas-de-privacidad')->sole()->title);
    }
}
