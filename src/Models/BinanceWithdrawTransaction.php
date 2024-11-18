<?php

namespace MystNov\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MystNov\Core\Enums\BinancePayReceiveMethod;

class BinanceWithdrawTransaction extends Model
{
    use HasFactory;

    protected $connection = 'mysql_main';

    protected $table = 'binance_withdraw_transactions';

    protected $fillable = [
        'withdrawal_id',
        'amount',
        'receive_method',
        'receive_address',
    ];

    /**
     * ----------------------------------------------
     * Attributes
     * ----------------------------------------------
     */

    public function getReceiveMethodTextAttribute()
    {
        return BinancePayReceiveMethod::options()[$this->receive_method];
    }

    public static function create(array $attributes = [])
    {
        return static::query()->create($attributes);
    }
}
