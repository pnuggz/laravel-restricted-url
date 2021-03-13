<?php

namespace Pnuggz\LaravelRestrictedUrl\Providers;

use Pnuggz\LaravelRestrictedUrl\ServiceRepository\Services\RestrictedUrlService;
use Pnuggz\LaravelRestrictedUrl\Traits\ServiceRepoDependencyInjectionTrait;
use Illuminate\Support\ServiceProvider;

class RestrictedUrlServiceProvider extends ServiceProvider
{
    use ServiceRepoDependencyInjectionTrait;

    const SERVICE_CLASSNAME = '\Pnuggz\LaravelRestrictedUrl\ServiceRepository\Services\RestrictedUrlService';
    const REPO_CLASSNAME    = '\Pnuggz\LaravelRestrictedUrl\ServiceRepository\Repositories\RestrictedUrlRepo';

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        // app('router')->aliasMiddleware('validateLimitedUseSignedUrl', ValidateLimitedUseSignedUrl::class);

        $this->publishes([
            __DIR__ .  '/../../config/config.php' => config_path('laravel_restricted_url.php'),
        ]);
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Register the main class to use with the facade
        $this->app->bind(RestrictedUrlService::class, function ($app) {
            return $this->getServiceWithDependencyInjections($app, self::SERVICE_CLASSNAME, self::REPO_CLASSNAME);
        });
    }
}