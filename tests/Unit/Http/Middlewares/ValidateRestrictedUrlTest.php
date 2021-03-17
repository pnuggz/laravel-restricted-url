<?php

namespace Pnuggz\LaravelRestrictedUrl\Tests\Unit\Http\Middlewares;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Pnuggz\LaravelRestrictedUrl\Facades\RestrictedUrlService;
use Pnuggz\LaravelRestrictedUrl\Http\Middlewares\ValidateRestrictedUrl;
use Pnuggz\LaravelRestrictedUrl\Models\BaseModel;
use Pnuggz\LaravelRestrictedUrl\Tests\BaseTestCase;
use Pnuggz\LaravelRestrictedUrl\Tests\Traits\RequestBuilderTrait;

class ValidateRestrictedUrlTest extends BaseTestCase 
{
    use RequestBuilderTrait;

    private Request $request;
    private ValidateRestrictedUrl $middleware;
    private $user;

    const USER_ID    = 1; 
    const ROUTE_NAME = 'signed-route';

    public function setUp(): void
    {
        parent::setUp();

        Route::any(self::ROUTE_NAME . '/', ['as' => self::ROUTE_NAME]);

        $this->request    = $this->createFullRequest(self::ROUTE_NAME);
        $this->middleware = new ValidateRestrictedUrl();
        $this->user       = (object) [
            'id' => self::USER_ID,
        ];
    }

    public function testMissingKeyFail(): void
    {
        $response = $this->middleware->handle($this->request, function () { });
        
        $this->assertEquals(500, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue(Arr::has($content, 'key_invalid'));
    }

    public function testRestrictedUrlNotSetFail(): void
    {
        $this->request->merge([
            'restricted_url_key' => rand()
        ]);

        $response = $this->middleware->handle($this->request, function () { });
        
        $this->assertEquals(500, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue(Arr::has($content, 'key_invalid'));
    }

    public function testRestrictedUrlIssetNoKeyFail(): void
    {
        $restricted_url = $this->createRestrictedUrl();

        $this->request->merge([
            'restricted_url_key' => rand()
        ]);

        $response = $this->middleware->handle($this->request, function () { });
        
        $this->assertEquals(500, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue(Arr::has($content, 'key_invalid'));
    }

    public function testRestrictedUrlIssetNoUserFail(): void
    {
        $restricted_url = $this->createRestrictedUrl();
        $key            = $restricted_url->route_key;

        $this->request->merge([
            'restricted_url_key' => $key,
        ]);

        $response = $this->middleware->handle($this->request, function () { });
        
        $this->assertEquals(500, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue(Arr::has($content, 'key_invalid'));
    }

    public function testRestrictedUrlExpiredFail(): void
    {
        $restricted_url = $this->createRestrictedUrl();
        $key            = $restricted_url->route_key;

        RestrictedUrlService::updateRestrictedUrl(
            $restricted_url,
            [
                'expires_at' => CarbonImmutable::now()->tz('utc')->subMinute()->format(BaseModel::STORAGE_DATE_TIME_FORMAT),
            ]
        );

        $this->request->merge([
            'restricted_url_key' => $key,
            'auth' => [
                'user' => $this->user,
            ],
        ]);

        $response = $this->middleware->handle($this->request, function () { });
        
        $this->assertEquals(500, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue(Arr::has($content, 'key_expired'));
    }

    public function testRestrictedUrlExceedLimitFail(): void
    {
        $restricted_url = $this->createRestrictedUrl();
        $key            = $restricted_url->route_key;

        RestrictedUrlService::updateRestrictedUrl(
            $restricted_url,
            [
                'access_count' => $restricted_url->access_limit,
            ]
        );

        $this->request->merge([
            'restricted_url_key' => $key,
            'auth' => [
                'user' => $this->user,
            ],
        ]);

        $response = $this->middleware->handle($this->request, function () { });
        
        $this->assertEquals(500, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue(Arr::has($content, 'key_expired'));
    }

    public function testRestrictedUrlSucceeds(): void
    {
        $restricted_url                      = $this->createRestrictedUrl();
        $key                                 = $restricted_url->route_key;
        $restricted_url_initial_access_count = $restricted_url->access_count;

        $this->request->merge([
            'restricted_url_key' => $key,
            'auth' => [
                'user' => $this->user,
            ],
        ]);

        $this->middleware->handle($this->request, function ($request) use ($key, $restricted_url_initial_access_count) {
            $restricted_url_updated = RestrictedUrlService::getRestrictedUrlByKey($key);
            $this->assertGreaterThan($restricted_url_initial_access_count, $restricted_url_updated->access_count);
        });
    }

    private function createRestrictedUrl()
    {
         $data = [
            'route_name' => self::ROUTE_NAME,
        ];

        return RestrictedUrlService::createRestrictedUrl($this->user, $data);
    }
}