<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Built assets are not part of the repository; tests render views without the Vite manifest.
        $this->withoutVite();

        // Tests load the local .env, which may hold live ERP credentials; every HTTP call must be faked.
        Http::preventStrayRequests();
    }
}
