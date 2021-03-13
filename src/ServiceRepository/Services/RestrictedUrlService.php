<?php

namespace Pnuggz\LaravelRestrictedUrl\ServiceRepository\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\MessageBag;
use Pnuggz\LaravelRestrictedUrl\ServiceRepository\Repositories\RestrictedUrlRepo;
use Webpatser\Uuid\Uuid;

class RestrictedUrlService
{
    protected $restricted_url_repo;

    public function __construct(RestrictedUrlRepo $restricted_url_repo)
    {
        $this->restricted_url_repo = $restricted_url_repo;
    }

    public function createRestrictedUrl($user, $data)
    {
        if (!$user) {
            return (new MessageBag())->add(
                'user_required',
                'The provided user is missing or invalid'
            );
        }

        $route_name_validation = $this->validateRouteNameExists($data);
        if ($route_name_validation instanceof MessageBag) {
            return $route_name_validation;
        }
        
        $expiry_in_seconds = Arr::get($data, 'expiry_in_seconds') ? Arr::get($data, 'expiry_in_seconds') : config('laravel_restricted_url.expiry_in_seconds');
        $access_limit      = Arr::get($data, 'access_limit') ? Arr::get($data, 'access_limit') : config('laravel_restricted_url.access_limit');
        
        $parsed_data = [
            'user_id'            => $user->id,
            'created_by_user_id' => $user->id,
            'route_name'         => Arr::get($data, 'route_name'),
            'key'                => $this->generateUniqueKey(),
            'expires_at'         => CarbonImmutable::now()->addSeconds($expiry_in_seconds),
            'access_limit'       => $access_limit,
        ];

        return $this->restricted_url_repo->insert($parsed_data);
    }

    public function validateRouteNameExists($data)
    {
        if(!Route::has(Arr::get($data, 'route_name'))) {
            return (new MessageBag())->add(
                'invalid_route_provided',
                'Route provided is invalid or missing.'
            );
        }
        return true;
    }

    public function generateUniqueKey()
    {
        return str_replace('-', '', Uuid::generate()->string);
    }
}