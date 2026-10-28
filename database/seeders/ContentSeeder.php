<?php

namespace Database\Seeders;

use App\Models\AboutPage;
use App\Models\CompanySetting;
use App\Models\LegalPage;
use Illuminate\Database\Seeder;

/**
 * Required site rows. Real texts are entered by the business from the admin.
 */
class ContentSeeder extends Seeder
{
    public function run(): void
    {
        CompanySetting::current();
        AboutPage::current();

        LegalPage::firstOrCreate(
            ['slug' => LegalPage::PRIVACY],
            ['title' => 'Políticas de privacidad'],
        );

        LegalPage::firstOrCreate(
            ['slug' => LegalPage::TERMS],
            ['title' => 'Términos y condiciones'],
        );
    }
}
