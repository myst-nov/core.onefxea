<?php

namespace MystNov\Core\Models;

class Notification extends Model
{
    protected $connection = 'mysql_main';

    protected $table = 'notifications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'morph_type',
        'morph_id',
        'content',
        'target_url',
        'important',
        'seen_at'
    ];

    /**
     * ------------------------------------------
     * Scopes
     * ------------------------------------------
     */

    /**
     * ------------------------------------------
     * Attributes
     * ------------------------------------------
     */

    /**
     * Return name field from first_name & last_name
     */
    public function getIsSeenAttribute()
    {
        return ! is_null($this->seen_at);
    }

    public function getTimeElapsedAttribute()
    {
        return _time_elapsed_string($this->created_at);
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
        return $this->hasOne(Member::class, 'id', 'morph_id')->withTrashed();
    }

    public function masterPage()
    {
        return $this->hasOne(MasterPage::class, 'id', 'morph_id')->withTrashed();
    }

    public static function create(array $attributes = [])
    {
        return static::query()->create($attributes);
    }
}
