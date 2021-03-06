<?php

namespace Pnuggz\LaravelRestrictedUrl\ServiceRepository\Repositories;

use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Pnuggz\LaravelRestrictedUrl\Models\BaseModel;
use Pnuggz\LaravelRestrictedUrl\Models\RestrictedUrl;

class RestrictedUrlRepo
{
    protected $restricted_url_entity;

    public function __construct()
    {
        $this->restricted_url_entity = new RestrictedUrl();
    }

    public function insert($data)
    {
        DB::beginTransaction();

        $cleanup_response = $this->cleanupExistingRestrictedUrls($data);
        if ($cleanup_response instanceof MessageBag) {
            DB::rollBack();
            return $cleanup_response;
        }

        $create_response = $this->restricted_url_entity->create($data);
        if (!$create_response) {
            DB::rollBack();
            return (new MessageBag())->add(
                'restricted_url_create_fail', 
                'Unable to create the restricted url. Please try again.'
            );
        }

        DB::commit();

        return $create_response;
    }

    public function getByKey($key)
    {
        return $this->restricted_url_entity->where('route_key', $key)
            ->first();
    }

    public function updateByRestrictedUrl(RestrictedUrl $restricted_url, $data)
    {
        $response = $restricted_url->update($data);
        if (!$response) {
            return (new MessageBag())->add(
                'restricted_url_update_fail', 
                'Unable to update the restricted url. Please try again.'
            ); 
        }
        return true;
    }

    private function cleanupExistingRestrictedUrls($data)
    {
        $now = CarbonImmutable::now();

        $urls = $this->restricted_url_entity
            ->where('user_id', Arr::get($data, 'user_id'))
            ->where('route_name', Arr::get($data, 'route_name'))
            ->where(function($q) use ($now) {
                $q->whereRaw('access_count > access_limit')
                    ->orWhere('expires_at', '<', $now->format(BaseModel::STORAGE_DATE_TIME_FORMAT));
            })
            ->get();

        foreach ($urls as $url) {
            $response = $url->delete();
            if (!$response) {
                return (new MessageBag())->add(
                    'restricted_url_cleanup_fail', 
                    'Unable to clean up the existing restricted urls. Please try again.'
                );
            }
        }

        return true;
    }
}