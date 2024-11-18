<?php

namespace MystNov\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Discount extends Model
{
    use HasFactory;

    protected $connection = 'mysql_main';

    protected $table = 'discounts';

    public $timestamps = false;

    protected $fillable = [
        'min_qty',
        'discount_rate',
        'plan_id',
    ];

    /**
     * -------------------------------------------------------
     * Relationships
     * -------------------------------------------------------
     */
    public function discount_plan(): BelongsTo
    {
        return $this->belongsTo(DiscountPlan::class);
    }
}
