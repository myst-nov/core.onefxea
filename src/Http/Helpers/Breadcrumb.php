<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

if (! function_exists('_get_breadcrumbs')) {
    function _get_breadcrumbs()
    {
        $namedRoutes = _get_named_routes_breadcrumb();
        $renamedRoutes = _rename_routes_breadcrumb($namedRoutes);
        $currentRouteName = request()->route()->getName();
        $currentRouteNameFormated = str_replace('.index', '', $currentRouteName);
        return $renamedRoutes[$currentRouteNameFormated];
    }
}

if (! function_exists('_get_page_name')) {
    function _get_page_name()
    {
        $namedRoutes = _get_named_routes_breadcrumb();
        $renamedRoutes = _rename_routes_breadcrumb($namedRoutes);
        $currentRouteName = request()->route()->getName();
        $currentRouteNameFormated = str_replace('.index', '', $currentRouteName);
        return $renamedRoutes[$currentRouteNameFormated][$currentRouteNameFormated];
    }
}

if (! function_exists('_get_named_routes_breadcrumbs')) {
    function _get_named_routes_breadcrumb()
    {
        $routes = Route::getRoutes();

        $namedRoutes = [];
        foreach ($routes as $routeName) {
            $name = $routeName->getName();
            $namedRoutes[$name] = $name;
        }

        return array_filter($namedRoutes);
    }
}

if (! function_exists('_rename_routes_breadcrumb')) {
    function _rename_routes_breadcrumb($namedRoutes)
    {
        $config = config('breadcrumbs');
        $renamedRoutes = [];
        foreach ($namedRoutes as $routeKey => $routeName) {
            // Remove index route
            $routeName = str_replace('.index', '', $routeName);

            // Explode route name to array name
            $nameArr = explode('.', $routeName);

            $nameArrFormated = [];

            $key = '';

            // Make title for name
            foreach ($nameArr as &$name) {
                if (strlen($key) > 0) {
                    $key .= '.';
                }
                $key .= $name;

                if (isset($config[$key])) {
                    $nameArrFormated[$key] = $config[$key];
                } else {
                    $name = Str::title($name);
                    $name = Str::replace('-', ' ', $name);

                    $nameArrFormated[$key] = $name;
                }
            }

            // Add routes to name list
            $renamedRoutes[$key] = $nameArrFormated;
        }

        return $renamedRoutes;
    }
}
