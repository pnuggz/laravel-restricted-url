<?php

namespace Pnuggz\LaravelRestrictedUrl\Tests\Unit\ServiceRepository\Repositories;

use Pnuggz\LaravelRestrictedUrl\ServiceRepository\Repositories\RestrictedUrlRepo;
use Pnuggz\LaravelRestrictedUrl\Tests\BaseTestCase;

class RestrictedUrlRepoTest extends BaseTestCase {

    public function testSucceeds(): void
    {
        $this->assertInstanceOf(RestrictedUrlRepo::class, new RestrictedUrlRepo());
    }

}