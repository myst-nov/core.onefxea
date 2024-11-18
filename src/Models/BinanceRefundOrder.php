<?php

namespace MystNov\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use MystNov\Core\Enums\RefundOrderStatus;

class BinanceRefundOrder extends Model
{
    use HasFactory;

    protected $connection = 'mysql_main';

    protected $table = 'binance_refund_orders';

    protected $fillable = [
        'member_id',
        'order_number',
        'binance_order_id',
        'payer_name',
        'payer_email',
        'payer_binance_id',
        'payer_account_id',
        'payer_phone_number',
        'payer_country_code',
        'currency',
        'amount',
        'reason',
        'transaction_time',
        'status',
    ];

    public function getStatusLabelAttribute()
    {
        return RefundOrderStatus::options()[$this->status];
    }

    public function getTransactedAtAttribute()
    {
        return date(config('app.datetime_format'), substr($this->transaction_time, 0, 10));
    }

    public function member()
    {
        return $this->hasOne(Member::class, 'id', 'member_id')->withTrashed();
    }

    public static function create(array $attributes = [], $transaction = null)
    {
        $attributes['member_id'] = Auth::user()->id;
        $attributes['status'] = RefundOrderStatus::REQUESTED;

        if ($transaction) {
            $attributes['payer_name'] = $transaction->payerInfo->name ?? null;
            $attributes['payer_email'] = $transaction->payerInfo->email ?? null;
            $attributes['payer_binance_id'] = $transaction->payerInfo->binanceId ?? null;
            $attributes['payer_account_id'] = $transaction->payerInfo->accountId;
            $attributes['payer_phone_number'] = $transaction->payerInfo->phoneNumber ?? null;
            $attributes['payer_country_code'] = $transaction->payerInfo->countryCode ?? 0;
            $attributes['currency'] = $transaction->currency;
            $attributes['amount'] = $attributes['amount'] ?? $transaction->amount;
            $attributes['transaction_time'] = $transaction->transactionTime;
        }

        return static::query()->create($attributes);
    }
}
