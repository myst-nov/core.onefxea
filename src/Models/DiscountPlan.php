<?php

namespace MystNov\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DiscountPlan extends Model
{
    use HasFactory;

    protected $connection = 'mysql_main';

    protected $table = 'discount_plans';

    protected $fillable = [
        'name',
        'description',
        'start_at',
        'end_at',
    ];

    /**
     * -------------------------------------------------------
     * Relationships
     * -------------------------------------------------------
     */
    public function discounts(): HasMany
    {
        return $this->hasMany(Discount::class, 'plan_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_discount_plan', 'plan_id');
    }
}
