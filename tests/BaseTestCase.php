<?php

namespace Pnuggz\LaravelRestrictedUrl\Tests;

use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\TestCase;
use Pnuggz\LaravelRestrictedUrl\Providers\RestrictedUrlServiceProvider;

class BaseTestCase extends TestCase {
    
    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate'); 

        Config::set('laravel_restricted_url', [
            'expiry_in_seconds' => 60,
            'access_limit'  => 2
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [
            RestrictedUrlServiceProvider::class
        ];
    }

}