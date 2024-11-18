<?php

namespace MystNov\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShareHoldingTransaction extends Model
{
    use HasFactory;

    protected $connection = 'mysql_main';

    protected $table = 'shareholding_transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'member_id',
        'month',
        'year',
        'stock',
        'point',
        'total_revenue',
        'total_profit',
    ];

    /**
     * ----------------------------------------------
     * Attributes
     * ----------------------------------------------
     */

    /**
     * ----------------------------------------------
     * Relationships
     * ----------------------------------------------
     */

    /**
     * ----------------------------------------------
     * Funcs
     * ----------------------------------------------
     */
}
