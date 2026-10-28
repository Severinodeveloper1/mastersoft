<?php

namespace Tests\Feature\Pages;

use App\Livewire\Claims\ClaimForm;
use App\Livewire\Contact\ContactForm;
use App\Mail\ClaimFiledNotification;
use App\Mail\ContactMessageNotification;
use App\Models\AboutPage;
use App\Models\Claim;
use App\Models\CompanySetting;
use App\Models\LegalPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class InformationalPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_page_shows_admin_copy_and_omits_empty_sections(): void
    {
        AboutPage::current()->update(['title' => 'Nuestra historia', 'description' => '<p>Contenido real</p>']);

        $this->get('/nosotros')->assertOk()->assertSee('Nuestra historia')->assertSee('Contenido real')
            ->assertDontSee('Misión')->assertDontSee('Distribuidor autorizado');
    }

    public function test_contact_page_shows_only_configured_details_and_a_form(): void
    {
        CompanySetting::current()->update(['phone' => '999111222', 'map_embed_url' => 'https://www.google.com/maps/embed?pb=test']);

        $this->get('/contactanos')->assertOk()->assertSee('999111222')->assertSee('Envía tu mensaje')
            ->assertSee('google.com/maps/embed')->assertDontSee('Preguntas frecuentes');
    }

    public function test_legal_pages_render_admin_content_and_missing_page_is_404(): void
    {
        LegalPage::factory()->create(['slug' => LegalPage::PRIVACY, 'title' => 'Privacidad real', 'content' => '<p>Texto legal</p>']);

        $this->get('/politicas-de-privacidad')->assertOk()->assertSee('Privacidad real')->assertSee('Texto legal');
        $this->get('/terminos-y-condiciones')->assertNotFound();
    }

    public function test_empty_legal_content_and_not_found_page_are_readable(): void
    {
        LegalPage::factory()->create(['slug' => LegalPage::TERMS, 'title' => 'Términos', 'content' => '']);

        $this->get('/terminos-y-condiciones')->assertOk()->assertSee('Contenido en preparación.');
        $this->get('/missing-page')->assertNotFound()->assertSee('Página no encontrada');
    }

    public function test_contact_submission_requires_privacy_consent(): void
    {
        Livewire::test(ContactForm::class)
            ->set('name', 'Ana Pérez')->set('email', 'ana@example.test')->set('phone', '999111222')
            ->set('subject', 'Otro')->set('message', 'Consulta')
            ->call('submit')->assertHasErrors(['privacyAccepted' => 'accepted']);
        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_contact_submission_persists_and_notifies_admin(): void
    {
        Mail::fake();
        CompanySetting::current()->update(['email' => 'tienda@example.test']);

        Livewire::test(ContactForm::class)
            ->set('name', 'Ana Pérez')->set('email', 'ana@example.test')->set('phone', '999111222')
            ->set('subject', 'Otro')->set('message', 'Consulta')->set('privacyAccepted', true)
            ->call('submit')->assertHasNoErrors()->assertSet('sent', true);

        $this->assertDatabaseHas('contact_messages', ['email' => 'ana@example.test', 'phone' => '+51 999111222']);
        Mail::assertSent(ContactMessageNotification::class, fn ($mail) => $mail->hasTo('tienda@example.test'));
    }

    public function test_claim_submission_requires_consent_and_minor_guardian_data(): void
    {
        Livewire::test(ClaimForm::class)->set('isMinor', true)->call('submit')
            ->assertHasErrors(['guardianName', 'guardianDocument', 'privacyAccepted']);
        $this->assertDatabaseCount('claims', 0);
    }

    public function test_claim_submission_files_through_claim_service_and_sends_both_copies(): void
    {
        Mail::fake();
        CompanySetting::current()->update(['claims_email' => 'reclamos@example.test', 'email' => 'tienda@example.test']);
        Livewire::test(ClaimForm::class)
            ->set('documentNumber', '12345678')->set('firstName', 'Ana')->set('paternalSurname', 'Pérez')
            ->set('maternalSurname', 'Díaz')->set('phone', '999111222')->set('email', 'ana@example.test')
            ->set('department', 'Amazonas')->set('province', 'Chachapoyas')->set('district', 'Chachapoyas')->set('address', 'Calle 1')
            ->set('goodDescription', 'Producto')->set('claimedAmount', '0')->set('claimType', 'complaint')
            ->set('detail', 'Detalle')->set('consumerRequest', 'Solicito solución')->set('privacyAccepted', true)
            ->call('submit')->assertHasNoErrors();

        $claim = Claim::query()->firstOrFail();
        $this->assertSame(6, strlen(explode('-', $claim->code)[0]));
        $this->assertNotNull($claim->response_due_on);
        $this->assertNotNull($claim->privacy_accepted_at);
        $this->assertFalse($claim->email_notification_accepted);
        Mail::assertSent(ClaimFiledNotification::class, 2);
        Mail::assertSent(ClaimFiledNotification::class, fn ($mail) => $mail->hasTo('ana@example.test'));
        Mail::assertSent(ClaimFiledNotification::class, fn ($mail) => $mail->hasTo('reclamos@example.test'));
    }
}
