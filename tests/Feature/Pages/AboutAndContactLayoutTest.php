<?php

namespace Tests\Feature\Pages;

use App\Models\AboutPage;
use App\Models\CompanySetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutAndContactLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_page_without_content_shows_header_and_empty_state(): void
    {
        $this->get('/nosotros')
            ->assertOk()
            ->assertSeeText('Nosotros')
            ->assertSeeText('Inicio')
            ->assertSeeText('Estamos preparando esta sección.')
            ->assertDontSeeText('Misión');
    }

    public function test_about_page_with_content_hides_the_empty_state(): void
    {
        AboutPage::current()->update([
            'title' => 'Tecnología y conectividad para tu negocio',
            'mission' => '<p>Proveer soluciones de cómputo.</p>',
        ]);

        $this->get('/nosotros')
            ->assertOk()
            ->assertSeeText('Tecnología y conectividad para tu negocio')
            ->assertSeeText('Misión')
            ->assertDontSeeText('Visión')
            ->assertDontSeeText('Estamos preparando esta sección.');
    }

    public function test_contact_page_keeps_its_structure_without_company_data(): void
    {
        $this->get('/contactanos')
            ->assertOk()
            ->assertSeeText('¡Estamos aquí para ti! ¿Cómo podemos ayudarte?')
            ->assertSeeText('Vamos a conectarnos')
            ->assertSeeText('Envía tu mensaje')
            ->assertDontSee('<iframe', false);
    }

    public function test_contact_page_shows_location_card_when_address_exists_but_no_map(): void
    {
        CompanySetting::current()->update([
            'address' => 'Jr. Ejemplo 123, Nueva Cajamarca',
            'whatsapp_number' => '51900000000',
        ]);

        $this->get('/contactanos')
            ->assertOk()
            ->assertSeeText('Nuestra ubicación')
            ->assertSeeText('Jr. Ejemplo 123, Nueva Cajamarca')
            ->assertSee('https://wa.me/51900000000', false)
            ->assertDontSee('<iframe', false);
    }
}
