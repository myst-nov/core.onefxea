<?php

namespace MystNov\Core\Models;

class OTP extends Model
{
    protected $connection = 'mysql_main';

    protected $table = 'otps';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'token',
        'member_id',
    ];
}
