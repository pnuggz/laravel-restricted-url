<?php

namespace Pnuggz\LaravelRestrictedUrl\Tests\Unit\Facades;

use Pnuggz\LaravelRestrictedUrl\Facades\RestrictedUrlService;
use Pnuggz\LaravelRestrictedUrl\Tests\BaseTestCase;

class RestrictedUrlServiceTest extends BaseTestCase {

    public function testBindingSucceeds(): void
    {
        $this->assertEquals(
            \Pnuggz\LaravelRestrictedUrl\ServiceRepository\Services\RestrictedUrlService::class, 
            get_class(RestrictedUrlService::getFacadeRoot())
        );
    }

}