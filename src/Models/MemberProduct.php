<?php

namespace MystNov\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use MystNov\Core\Enums\ProductType;

class MemberProduct extends Model
{
    use HasFactory;

    protected $connection = 'mysql_main';

    protected $table = 'member_products';

    /**
     * Belong to member
     */
    public function member()
    {
        return $this->hasOne(Member::class, 'id', 'member_id');
    }

    /**
     * Belong to product
     */
    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    /**
     * Has many orders:
     * 1 Buy new order
     * Many extend order
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class);
    }

    /**
     * -------------------------------------------------------
     * Attributes
     * -------------------------------------------------------
     */

    public function getProductTypeLabelAttribute()
    {
        return ProductType::options()[$this->product_type];
    }
}
