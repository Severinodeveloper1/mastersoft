<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Built assets are not part of the repository; tests render views without the Vite manifest.
        $this->withoutVite();
    }
}
