<?php

namespace Pnuggz\LaravelRestrictedUrl\Tests\Unit\Models;

use Pnuggz\LaravelRestrictedUrl\Models\RestrictedUrl;
use Pnuggz\LaravelRestrictedUrl\Tests\BaseTestCase;

class RestrictedUrlTest extends BaseTestCase {

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testSucceeds(): void
    {
        $this->assertInstanceOf(RestrictedUrl::class, new RestrictedUrl());
    }
}