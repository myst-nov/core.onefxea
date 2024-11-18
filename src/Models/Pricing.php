<?php

namespace MystNov\Core\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pricing extends Model
{
    protected $connection = 'mysql_main';

    protected $table = 'pricing';

    protected $fillable = [
        'page_id',
        'product_id',
        'qty',
        'description',
    ];

    /**
     * Get the product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the product
     */
    public function masterPage(): BelongsTo
    {
        return $this->belongsTo(MasterPage::class);
    }
}
