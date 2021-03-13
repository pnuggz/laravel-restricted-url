<?php

namespace Pnuggz\LaravelRestrictedUrl\Tests\Unit\ServiceRepository\Services;

use Illuminate\Support\Facades\Route;
use Pnuggz\LaravelRestrictedUrl\Facades\RestrictedUrlService;
use Pnuggz\LaravelRestrictedUrl\Models\RestrictedUrl;
use Pnuggz\LaravelRestrictedUrl\Tests\BaseTestCase;

class RestrictedUrlServiceTest extends BaseTestCase {

    public function setUp(): void
    {
        parent::setUp();

        Route::any('test-route', ['as' => 'test-route']);
        Route::any('signed-route/{user}', ['as' => 'signed-route']);
    }

    public function testSucceeds(): void
    {
        $this->assertInstanceOf(RestrictedUrlService::class, new RestrictedUrlService());
    }

    public function testCreateRestrictedUrl(): void
    {
        $user = $this->createUser();

        $data = [
            'route_name' => 'signed-route'
        ];

        $response = RestrictedUrlService::createRestrictedUrl($user, $data);

        $this->assertInstanceOf(RestrictedUrl::class, $response);
    }

    public function createUser() 
    {
        return (object) [
            'id'    =>  rand()
        ];
    }
}