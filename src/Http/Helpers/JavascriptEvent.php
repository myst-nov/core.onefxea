<?php


if (! function_exists('_open_new_window')) {
    /**
     * Open new window by JS
     */
    function _open_new_window($url)
    {
        return "javascript:window.open('$url', '_blank')";
    }
}

if (! function_exists('_close_window_and_reload_opener')) {
    /**
     * Close current window and reload opener window
     */
    function _close_window_and_reload_opener()
    {
        return 'javascript:window.opener.location.reload();window.close();';
    }
}

if (! function_exists('_close_window')) {
    /**
     * Close current window
     */
    function _close_window()
    {
        return 'javascript:window.close();';
    }
}
