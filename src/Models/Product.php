<?php

namespace MystNov\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'mysql_main';

    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'code',
        'name',
        'price',
        'description',
        'disabled',
    ];

    /**
     * ----------------------------------------------
     * Scopes
     * ----------------------------------------------
     */

    public function scopeIsLicense($query)
    {
        return $query->where('type', 'license');
    }

    public function scopeIsVps($query)
    {
        return $query->where('type', 'vps');
    }

    public function scopeIsActive($query)
    {
        return $query->where('disabled', false);
    }

    /**
     * ----------------------------------------------
     * Attributes
     * ----------------------------------------------
     */

    public function getIsActiveAttribute()
    {
        return $this->disabled == 0;
    }

    /**
     * ----------------------------------------------
     * Relationships
     * ----------------------------------------------
     */
    public function masterPages(): BelongsToMany
    {
        return $this->belongsToMany(MasterPage::class, 'master_page_product', 'product_id', 'page_id');
    }

    public function discountPlans(): BelongsToMany
    {
        return $this->belongsToMany(DiscountPlan::class, 'product_discount_plan', 'product_id', 'plan_id');
    }

    /**
     * ----------------------------------------------
     * Funcs
     * ----------------------------------------------
     */

    public function filter()
    {
        $query = $this;

        if (request()->filled('search')) {
            $query = $query->where(function ($cond) {
                return $cond->where('code', 'LIKE', _search_text(request()->search))
                    ->orWhere('name', 'LIKE', _search_text(request()->search));
            });
        }

        return $query;
    }
}
