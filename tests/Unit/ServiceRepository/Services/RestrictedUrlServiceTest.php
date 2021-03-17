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

    public function testCreateRestrictedUrlSucceeds(): void
    {
        $user = $this->createUser();

        $data = [
            'route_name' => 'signed-route'
        ];

        $response = RestrictedUrlService::createRestrictedUrl($user, $data);

        $this->assertInstanceOf(RestrictedUrl::class, $response);
    }

    public function testGetRestrictedUrlByKeySucceeds(): void
    {
        $restricted_url = $this->createRestrictedUrl();
        $key = $restricted_url->route_key;

        $restricted_url_by_key = RestrictedUrlService::getRestrictedUrlByKey($key);

        $this->assertInstanceOf(RestrictedUrl::class, $restricted_url_by_key);
        $this->assertEquals($key, $restricted_url_by_key->route_key);
    }

    private function createRestrictedUrl()
    {
        $user = $this->createUser();

        $data = [
            'route_name' => 'signed-route'
        ];

        return RestrictedUrlService::createRestrictedUrl($user, $data);
    }

    private function createUser() 
    {
        return (object) [
            'id'    =>  rand()
        ];
    }
}