<?php

namespace Pnuggz\LaravelRestrictedUrl\Models;

use Pnuggz\LaravelRestrictedUrl\Models\BaseModel;

class RestrictedUrl extends BaseModel
{
    protected $table = 'restricted_urls';

    protected $dates = [
        'first_accessed_at', 
        'last_reaccessed_at',
        'expires_at',
    ];

    protected $fillable = [
        'user_id',
        'route_name',
        'key',
        'expires_at',
        'access_limit',
        'access_count',
        'first_accessed_by_ip',
        'first_accessed_by_user_id',
        'first_accessed_at',
        'last_reaccessed_at',
        'last_reaccessed_by_ip',
        'last_reaccessed_by_user_id',
        'created_by_user_id',
    ];

    /**
     * Generates and returns the route needed
     * @param  Array|array $params 
     */
    public function getRestrictedUrlString(Array $params = [])
    {
        $params['user'] = $this->user_id;
        return route($this->route_name, $params) . '?restricted_url_key=' . $this->key;
    }
}