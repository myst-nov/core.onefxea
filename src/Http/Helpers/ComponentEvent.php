<?php


use Illuminate\Support\Str;

if (! function_exists('_active_sidebar')) {
    function _active_sidebar($routes, $action = ' active', $refuse = '')
    {
        if (is_array($routes)) {
            foreach ($routes as $route) {
                if (request()->route()->getName() === $route || Str::startsWith(request()->route()->getName(), str_replace('index', '', $route))) {
                    return $action;
                }
            }
        } else {
            if (request()->route()->getName() === $routes || Str::startsWith(request()->route()->getName(), str_replace('index', '', $routes))) {
                return $action;
            }
        }

        return $refuse;
    }
}
