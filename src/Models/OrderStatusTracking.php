<?php

namespace MystNov\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MystNov\Core\Enums\OrderStatus;
use MystNov\Core\Enums\OrderStatusDescription;

class OrderStatusTracking extends Model
{
    use HasFactory;

    protected $connection = 'mysql_main';

    protected $table = 'order_status_tracking';

    protected $fillable = [
        'admin_id',
        'order_id',
        'status'
    ];

    /**
     * -------------------------------------------------------
     * Attributes
     * -------------------------------------------------------
     */
    public function getStatusLabelAttribute()
    {
        return OrderStatus::options()[$this->status];
    }

    public function getStatusDescriptionAttribute()
    {
        return OrderStatusDescription::options()[$this->status];
    }

    /**
     * ---------------------------------------------------------
     * Func
     * ---------------------------------------------------------
     */
    public function insert($order, $adminId = null)
    {
        $record = $this->refresh();
        $record->admin_id = $adminId;
        $record->order_id = $order->id;
        $record->status = $order->status;
        $record->save();
    }
}
