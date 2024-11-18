<?php

namespace MystNov\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Template extends Model
{
    use HasFactory;

    protected $connection = 'mysql_main';

    protected $table = 'templates';

    /**
     * ----------------------------------------------
     * Scopes
     * ----------------------------------------------
     */

    public function scopeIsAvailable($query)
    {
        return $query->whereNull('disabled_at');
    }
}
