<?php

namespace MystNov\Core\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use MystNov\Core\Enums\OrderPaymentMethod;
use MystNov\Core\Enums\OrderStatus;
use MystNov\Core\Enums\OrderStatusDescription;
use MystNov\Core\Enums\OrderType;
use MystNov\Core\Enums\ProductType;

class Order extends Model
{
    use HasFactory;

    protected $connection = 'mysql_main';

    protected $table = 'orders';

    protected $fillable = [
        'code',
        'page_id',
        'member_id',
        'product_id',
        'product_account_size',
        'product_code',
        'product_name',
        'product_price',
        'product_type',
        'payment_method',
        'payment_expires_at',
        'qty',
        'status',
        'type',
        'subtotal',
        'total',
        'member_product_id'
    ];

    public function filter()
    {
        $query = $this;

        if (request()->filled('search')) {
            $query = $query->where(function ($cond) {
                return $cond->where('code', 'LIKE', _search_text(request()->search))
                    ->orWhere('package_code', 'LIKE', _search_text(request()->search))
                    ->orWhere('package_name', 'LIKE', _search_text(request()->search));
            });
        }

        // if($request->filled('name')) {
        //     $query = $query->where(function($cond) use ($request) {
        //         return $cond->where('first_name', 'LIKE', _search_text($request->name))->orWhere('last_name', 'LIKE', _search_text($request->name));
        //     });
        // }

        // if($request->filled('email')) {
        //     $query = $query->where('email', 'LIKE', _search_text($request->email));
        // }

        // if($request->filled('phone')) {
        //     $query = $query->where('phone', 'LIKE', _search_text($request->phone));
        // }

        // if($request->filled('status') && count($request->status) === 1) {
        //     if(in_array('active', $request->status)) {
        //         $query = $query->active();
        //     }

        //     if(in_array('disable', $request->status)) {
        //         $query = $query->disable();
        //     }
        // }

        return $query;
    }

    public function getExpiresAt()
    {
        return Carbon::now()->addDay();
    }

    public static function create(array $attributes = [])
    {
        return static::query()->create($attributes);
    }

    /**
     * -------------------------------------------------------
     * Attributes
     * -------------------------------------------------------
     */

    public function getPaymentMethodLabelAttribute()
    {
        return OrderPaymentMethod::options()[$this->payment_method];
    }

    public function getTypeLabelAttribute()
    {
        return OrderType::options()[$this->type];
    }

    public function getStatusLabelAttribute()
    {
        return OrderStatus::options()[$this->status];
    }

    public function getStatusDescriptionAttribute()
    {
        return OrderStatusDescription::options()[$this->status];
    }

    public function getStatusGroupAttribute()
    {
        if (in_array($this->status, [OrderStatus::ORDERED->value, OrderStatus::CUSTOMER_PAID->value, OrderStatus::PAID->value])) {
            return OrderStatus::ORDERED->value;
        }
        if (in_array($this->status, [OrderStatus::PROCESSING->value])) {
            return OrderStatus::PROCESSING->value;
        }
        if (in_array($this->status, [OrderStatus::COMPLETED->value])) {
            return OrderStatus::COMPLETED->value;
        }
        if (in_array($this->status, [OrderStatus::CANCELLED->value, OrderStatus::FAILED->value])) {
            return OrderStatus::FAILED->value;
        }
    }

    public function getProductTypeLabelAttribute()
    {
        return ProductType::options()[$this->product_type];
    }

    /**
     * -------------------------------------------------------
     * Relationships
     * -------------------------------------------------------
     */

    public function member()
    {
        return $this->hasOne(Member::class, 'id', 'member_id')->withTrashed();
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id')->withTrashed();
    }

    public function statusTracking()
    {
        return $this->hasMany(OrderStatusTracking::class, 'order_id')->orderBy('id', 'asc');
    }

    public function masterPage()
    {
        return $this->hasOne(MasterPage::class, 'id', 'page_id')->whereNull('disabled_at')->whereNull('locked_at');
    }

    /**
     * Belong to many member product
     */
    public function memberProducts(): BelongsToMany
    {
        return $this->belongsToMany(MemberProduct::class);
    }
}
