<?php

namespace Pnuggz\LaravelRestrictedUrl\Http\Middlewares;

use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\MessageBag;
use Pnuggz\LaravelRestrictedUrl\Facades\RestrictedUrlService;

class ValidateRestrictedUrl
{

    public function handle($request, Closure $next)
    {
        /**
         * Validate that a key is provided
         */
        $key = $request->get('restricted_url_key');
        if (!$key) {
            return $this->returnErrorResponse(
                'key_invalid',
                'The restricted url key is invalid',
            );
        }
        
        $restricted_url = RestrictedUrlService::getRestrictedUrlByKey($key);
        
        /**
         * Validate that a key is valid
         */
        if(!$restricted_url) {
            return $this->returnErrorResponse(
                'key_invalid',
                'The restricted url key is invalid',
            );
        }
        
        /**
         * Validate that the route name being access matches the route name
         * of the key
         */
        if($request->route()->getName() !== $restricted_url->route_name) {
            return $this->returnErrorResponse(
                'key_invalid',
                'The restricted url key is invalid',
            );
        }

        /**
         * Validate that key belongs to the user
         */
        $auth = $request->get('auth', []);
        $auth_user = Arr::get($auth, 'user');
        if (!$auth_user || $auth_user->id != $restricted_url->user_id) {
            return $this->returnErrorResponse(
                'key_invalid',
                'The restricted url key is invalid',
            );
        }

        /**
         * Validate that key is not expired
         */
        if ($restricted_url->expires_at && CarbonImmutable::now('utc') > $restricted_url->expires_at) {
            return $this->returnErrorResponse(
                'key_expired',
                'The restricted url key is expired',
            );
        }

        
        /**
         * Validate that url access count has not exceeded the access limit
         */
        $new_access_count = $restricted_url->access_count + 1;
        if ($restricted_url->access_limit && $new_access_count > $restricted_url->access_limit) {
            return $this->returnErrorResponse(
                'key_expired',
                'The restricted url key is expired',
            );
        }

        $update_response = RestrictedUrlService::setRestrictedUrlAccessCountWithUser($restricted_url, $auth_user->id, $request->ip());
        if ($update_response instanceof MessageBag) {
            return $this->returnErrorResponse(
                'internal_server_error',
                'Internal server error',
            );
        }

        return $next($request);
    }

    public function returnErrorResponse($key, $message) {
        $message_bag = (new MessageBag())->add($key, $message);
        $serialized  = json_encode($message_bag);
        
        return new Response($serialized, 500, [
            'Content-Type' => 'application/json'
        ]);
    }

}