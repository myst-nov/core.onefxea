<?php

namespace MystNov\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class CoinPayment extends Model
{
    use HasFactory;

    protected $connection = 'mysql_main';

    protected $table = 'coinpayment_transactions';
}
