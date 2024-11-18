<?php

namespace MystNov\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MystNov\Core\Enums\BinancePayStatus;
use MystNov\Core\Enums\CoinPaymentsStatus;
use MystNov\Core\Enums\PayPalStatus;
use MystNov\Core\Enums\PointOrderPaymentMethod;
use MystNov\Core\Enums\PointOrderSource;

class PointOrder extends Model
{
    use HasFactory;

    protected $connection = 'mysql_main';

    protected $table = 'point_orders';

    protected $fillable = [
        'member_id',
        'source',
        'point',
        'payment_method',
        'payment_url',
        'coinpayments_order_id',
        'coinpayments_status',
        'binance_order_number',
        'binance_status',
        'paypal_token',
        'paypal_status',
        'order_id',
        'network_member_id',
    ];

    /**
     * --------------------------------------------
     * Attributes
     * --------------------------------------------
     */

    public function getIsCommissionAttribute()
    {
        return $this->source == PointOrderSource::COMMISSION->value;
    }

    public function getPaypalStatusLabelAttribute()
    {
        return PayPalStatus::options()[$this->paypal_status] ?? null;
    }

    public function getCoinpaymentsStatusLabelAttribute()
    {
        return CoinPaymentsStatus::options()[$this->coinpayments_status] ?? null;
    }

    public function getBinanceStatusLabelAttribute()
    {
        return BinancePayStatus::options()[$this->binance_status] ?? null;
    }

    public function getPaymentMethodLabelAttribute()
    {
        return PointOrderPaymentMethod::options()[$this->payment_method] ?? null;
    }

    public function getSourceLabelAttribute()
    {
        return PointOrderSource::options()[$this->source];
    }

    public function getOrderInfoAttribute()
    {
        return $this->order;
    }

    public function getOrderUrlAttribute()
    {
        return route('order.show', $this->order_id);
    }


    /**
     * --------------------------------------------
     * Relationships
     * --------------------------------------------
     */

    /**
     * Relationship to members table by member_id
     */
    public function member()
    {
        return $this->hasOne(Member::class, 'id', 'member_id')->withTrashed();
    }

    /**
     * Relationship to orders table by order_id
     */
    public function order()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

    /**
     * Relationship to point_orders table by point_order_id
     */
    public function networkMember()
    {
        return $this->hasOne(Member::class, 'id', 'network_member_id')->withTrashed();
    }

    /**
     * --------------------------------------------
     * Funcs
     * --------------------------------------------
     */

    public function filter()
    {
        $memberTable = (new Member)->getTable();

        // $orderTable
        $query = $this->join($memberTable, "{$this->table}.member_id", "{$memberTable}.id");

        if (request()->filled('search')) {
            $query = $query->where(function ($cond) {
                return $cond->where('members.first_name', 'LIKE', _search_text(request()->search))
                    ->orWhere('members.last_name', 'LIKE', _search_text(request()->search))
                    ->orWhere('members.email', 'LIKE', _search_text(request()->search));
            });
        }

        return $query;
    }

    public static function create(array $attributes = [])
    {
        return static::query()->create($attributes);
    }
}
