<?php

namespace MystNov\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MystNov\Core\Enums\OrderStatus;

class Activity extends Model
{
    use HasFactory;

    protected $connection = 'mysql_main';

    protected $table = 'activity_logs';

    public function getDataDecodedAttribute()
    {
        return json_decode($this->data);
    }

    public function getOrderOldStatusAttribute()
    {
        return OrderStatus::options()[$this->data_decoded->old_status];
    }

    public function getOrderStatusAttribute()
    {
        return OrderStatus::options()[$this->data_decoded->status];
    }
}
