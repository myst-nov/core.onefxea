<?php

use Illuminate\Support\Facades\Auth;

if (! function_exists('is_admin')) {
    /**
     * Check if current logged in user is admin
     */
    function is_admin()
    {
        return Auth::getDefaultDriver() == 'admin';
    }
}

if (! function_exists('is_master')) {
    /**
     * Check if current logged in user is master ib
     */
    function is_master()
    {
        return Auth::getDefaultDriver() == 'master';
    }
}
