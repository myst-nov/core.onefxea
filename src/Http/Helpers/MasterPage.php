<?php

use App\Support\Facades\MasterPage as FacadesMasterPage;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use MystNov\Core\Models\MasterPage;
use Illuminate\Support\Str;

if (! function_exists('_is_subdomain')) {
    /**
     * Check if current url or $url is a subdomain
     */
    function _is_subdomain()
    {
        $host = _url_non_port(request()->server('HTTP_HOST'));
        $domain = _url_domain(config('app.url'));

        return ($host != $domain && Str::endsWith($host, $domain));
    }
}

if (! function_exists('_is_page_domain')) {
    /**
     * Check if current url or $url is a subdomain
     */
    function _is_page_domain()
    {
        $host = _url_non_port(request()->server('HTTP_HOST'));
        $domain = _url_domain(config('app.url'));

        return ($host != $domain && ! Str::endsWith($host, $domain));
    }
}

if (! function_exists('_is_master_page')) {
    /**
     * Check if current url is a master page url
     */
    function _is_master_page()
    {
        return (Route::current()->parameter('pageid') !== null || request()->is(config('app.path.master', 'm') . '/*'));
    }
}

if (! function_exists('_page_id')) {
    /**
     * Get page_id from route
     */
    function _page_id()
    {
        if (Route::current()) {
            return Route::current()->parameter('pageid');
        }
        return null;
    }
}

if (! function_exists('_master_page_id')) {
    /**
     * Get id of Master Page
     */
    function _master_page_id()
    {
        $pageId = _page_id();

        if (! is_null($pageId)) {
            $id = FacadesMasterPage::page()->id ?? null;

            if (is_null($id)) {
                $id = MasterPage::where('page_id', $pageId)->withTrashed()->first()->id;
            }

            return $id;
        }

        return null;
    }
}

if (! function_exists('_set_default_parameter_url')) {
    /**
     * Set default pageid to all route
     */
    function _set_default_parameter_url()
    {
        $pageId = Route::current()->parameter('pageid');

        URL::defaults(['pageid' => $pageId]);
    }
}

if (! function_exists('_r')) {
    /**
     * Replace route name to master page route name when access in master management page
     */
    function _r($routeName)
    {
        if (_is_master_page()) {
            return 'master.' . $routeName;
        }

        return $routeName;
    }
}
