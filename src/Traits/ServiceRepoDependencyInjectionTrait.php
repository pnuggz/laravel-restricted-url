<?php

namespace Pnuggz\LaravelRestrictedUrl\Traits;

trait ServiceRepoDependencyInjectionTrait
{
    public function getServiceWithDependencyInjections($app, $service_classname, $repo_classname = null)
    {
        if ($repo_classname) {
            $repo = new $repo_classname();
            return new $service_classname($repo);
        } else {
            return new $service_classname();
        }
    }
}