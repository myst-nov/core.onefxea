<?php

namespace MystNov\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Option extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'mysql_main';

    protected $table = 'options';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'value',
        'page_id'
    ];

    public function getValue($name, $pageId = null)
    {
        if (is_null($pageId)) {
            $pageId = _master_page_id();
        }
        return $this->where('name', $name)->where('page_id', $pageId)->first()->value ?? null;
    }
}
