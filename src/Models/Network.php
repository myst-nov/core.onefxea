<?php

namespace MystNov\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Network extends Model
{
    use HasFactory;

    protected $connection = 'mysql_main';

    protected $table = 'networks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'member_id',
        'relate_member_id',
    ];

    public $timestamps = false;

    public function referrer()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function referred()
    {
        return $this->belongsTo(Member::class, 'relate_member_id');
    }
}
