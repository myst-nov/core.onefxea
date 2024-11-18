<?php

namespace MystNov\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShareHolding extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'mysql_main';

    protected $table = 'shareholdings';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'member_id',
        'value',
    ];

    /**
     * ----------------------------------------------
     * Attributes
     * ----------------------------------------------
     */

    /**
     * ----------------------------------------------
     * Relationships
     * ----------------------------------------------
     */

    /**
     * ----------------------------------------------
     * Funcs
     * ----------------------------------------------
     */
    public function upsert($input)
    {
        $rec = $this->updateOrCreate(
            ['member_id' => $input->member_id],
            ['value' => $input->value]
        );

        return $rec;
    }

    public function remove($memberId)
    {
        return $this->refresh()->where('member_id', $memberId)->delete();
    }

    public function restore($memberId)
    {
        return $this->refresh()->where('member_id', $memberId)->restore();
    }
}
