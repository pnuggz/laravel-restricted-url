<?php

namespace Pnuggz\LaravelRestrictedUrl\Tests\Traits;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as Router;

trait RequestBuilderTrait
{
    /**
     * Will create a request with the correct fullUrl, route parameters and input parameters.
     *
     * @example RequestBuilder::create('post.show', ['postId' => 1], PostShowRequest::class);
     * @example RequestBuilder::create('post.index');
     *
     * @param string $routeName Name of the route.
     * @param array $parameters Array of route or input parameters.
     * @param string $class Request class.
     * @throws \Exception If the route name doesn't exist.
     * @return Request
     */
    public function createFullRequest(string $routeName, array $parameters = [], string $class = Request::class)
    {
        // Find the route properties.
        $route = Router::getRoutes()->getByName($routeName);

        throw_if(is_null($route), new \Exception("[RequestBuilder] Couldn't find route by the name of {$routeName}."));

        // Recreate the full url
        $fullUrl = route($routeName, $parameters);

        $method = $route->methods()[0];
        $uri = $route->uri;

        $request = $class::create($fullUrl);
        $request->setRouteResolver(function () use ($request, $method, $uri, $routeName) {
            // Associate Route to request so we can access route parameters.
            $route = (new Route($method, $uri, []))->bind($request);
            $route->name($routeName);
            return $route;
        });
        

        return $request;
    }
}