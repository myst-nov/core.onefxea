<?php

namespace MystNov\Core\Models;

use Illuminate\Support\Facades\Auth;

class OTP extends Model
{
    protected $connection = 'mysql_main';

    protected $table = 'otps';

    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'token',
        'member_id',
        'verification_url'
    ];

    /**
     * Relationship to members table by member_id
     */
    public function member()
    {
        return $this->hasOne(Member::class, 'id', 'member_id');
    }

    public static function create(array $attributes = [])
    {
        $attributes['token'] = bcrypt($attributes['token']);
        $attributes['member_id'] = $attributes['member_id'] ?? Auth::user()->id;
        $attributes['verification_url'] = $attributes['verification_url'] ?? url()->current();

        return static::query()->create($attributes);
    }
}
