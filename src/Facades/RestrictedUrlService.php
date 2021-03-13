<?php

namespace Pnuggz\LaravelRestrictedUrl\Facades;

use Illuminate\Support\Facades\Facade;

class RestrictedUrlService extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Pnuggz\LaravelRestrictedUrl\ServiceRepository\Services\RestrictedUrlService::class;
    }
}