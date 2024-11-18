<?php

namespace MystNov\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use MystNov\Core\Enums\WithdrawMethod;
use MystNov\Core\Enums\WithdrawStatus;

class Withdrawal extends Model
{
    use HasFactory;

    protected $connection = 'mysql_main';

    protected $table = 'withdrawal';

    protected $fillable = [
        'member_id',
        'page_id',
        'amount',
        'withdraw_method',
        'status',
    ];

    /**
     * ----------------------------------------------
     * Attributes
     * ----------------------------------------------
     */

    public function getMethodTextAttribute()
    {
        return WithdrawMethod::options()[$this->withdraw_method];
    }

    public function getStatusTextAttribute()
    {
        return WithdrawStatus::options()[$this->status];
    }

    /**
     * ----------------------------------------------
     * Relationships
     * ----------------------------------------------
     */

    public function member()
    {
        return $this->hasOne(Member::class, 'id', 'member_id')->withTrashed();
    }

    public function binanceWithdrawTransaction()
    {
        return $this->hasOne(BinanceWithdrawTransaction::class, 'withdrawal_id', 'id');
    }

    public static function create(array $attributes = [])
    {
        $attributes['member_id'] = $attributes['member_id'] ?? Auth::user()->id;
        $attributes['page_id'] = $attributes['page_id'] ?? Auth::user()->page_id;

        return static::query()->create($attributes);
    }
}
