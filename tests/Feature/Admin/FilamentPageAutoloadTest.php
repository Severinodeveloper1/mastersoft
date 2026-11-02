<?php

namespace Tests\Feature\Admin;

use Composer\Autoload\ClassLoader;
use Filament\Facades\Filament;
use Tests\TestCase;

/**
 * With the Filament component cache (`filament:optimize`), a page class is loaded by name without its
 * resource file being loaded first. Every page must therefore live in its own PSR-4 file, or the admin
 * answers 500 with "Target class [...] does not exist".
 */
class FilamentPageAutoloadTest extends TestCase
{
    public function test_every_admin_resource_page_lives_in_its_own_autoloadable_file(): void
    {
        $loader = collect(ClassLoader::getRegisteredLoaders())->first();
        $resources = Filament::getPanel('admin')->getResources();
        $this->assertNotEmpty($resources);

        $missing = [];
        foreach ($resources as $resource) {
            foreach ($resource::getPages() as $page) {
                $class = $page->getPage();
                $file = $loader->findFile($class);
                if ($file === false || pathinfo($file, PATHINFO_FILENAME) !== class_basename($class)) {
                    $missing[] = $class;
                }
            }
        }

        $this->assertSame([], $missing, 'Page classes without their own PSR-4 file.');
    }
}
