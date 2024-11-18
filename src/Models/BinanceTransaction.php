<?php

namespace MystNov\Core\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MystNov\Core\Enums\BinancePayStatus;

class BinanceTransaction extends Model
{
    use HasFactory;

    protected $connection = 'mysql_main';

    protected $table = 'binance_transactions';

    protected $fillable = [
        'order_number',
        'binance_order_id',
        'amount',
        'amount_received',
        'amount_remaining',
        'currency',
        'api_response',
        'status',
        'expired_at',
    ];

    public function getExpiresAt()
    {
        return Carbon::now()->addDays(30);
    }

    public static function create(array $attributes = [])
    {
        $attributes['amount_remaining'] = $attributes['amount_remaining'] ?? $attributes['amount'];
        $attributes['currency'] = 'USDT';
        $attributes['status'] = $attributes['status'] ?? BinancePayStatus::WAIT_FOR_FUNDS;

        return static::query()->create($attributes);
    }
}
