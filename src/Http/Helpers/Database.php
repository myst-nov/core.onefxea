<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use MystNov\Core\Models\Option;

if (! function_exists('_get_option')) {
    /**
     * Get Option by name
     */
    function _get_option($name)
    {
        return Option::where('name', $name)->ofMasterPage()->first()->value ?? null;
    }
}

if (! function_exists('_set_option')) {
    /**
     * Set Option by name
     */
    function _set_option($name, $value)
    {
        Option::updateOrCreate(
            ['name' => $name, 'page_id' => _master_page_id()],
            ['value' => $value, 'page_id' => _master_page_id()]
        );
    }
}

if (! function_exists('_delete_option')) {
    /**
     * Destroy Option
     */
    function _delete_option($name)
    {
        Option::where('name', $name)->ofMasterPage()->delete();
    }
}

if (! function_exists('_log_activity')) {
    /**
     * Log Activity
     */
    function _log_activity($activity, $data = null)
    {
        DB::table('activity_logs')->insert([
            'member_id'  => Auth::user()->id,
            'activity'   => $activity,
            'data'       => $data ? json_encode($data) : null,
            'ip'         => request()->ip(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
