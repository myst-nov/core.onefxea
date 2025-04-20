<?php

namespace MystNov\Core\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use MystNov\Core\Enums\LinkedSite;

class LinkedAccount extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_main';

    protected $table = 'linked_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'member_id',
        'page_id',
        'linked_site',
        'username'
    ];

    /**
     * ------------------------------------------
     * Attribute
     * ------------------------------------------
     */
    public function getLinkedSiteTitleAttribute()
    {
        return LinkedSite::options()[$this->linked_site];
    }


    /**
     * ------------------------------------------
     * Relationships
     * ------------------------------------------
     */

    /**
     * This member belong to a Master Page
     *
     * @return void
     */
    public function member()
    {
        return $this->hasOne(Member::class, 'id', 'member_id')->withTrashed();
    }

    public function masterPage()
    {
        return $this->hasOne(MasterPage::class, 'id', 'page_id')->withTrashed();
    }

    public static function create(array $attributes = [])
    {
        $attributes['member_id'] = auth()->user()->id;
        $attributes['page_id'] = auth()->user()->page_id;

        return static::query()->create($attributes);
    }
}
