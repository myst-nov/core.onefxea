<?php

namespace MystNov\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class TemplateField extends Model
{
    use HasFactory;

    protected $connection = 'mysql_main';

    protected $table = 'template_fields';

    public $timestamps = false;

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
