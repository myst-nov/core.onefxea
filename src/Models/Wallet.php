<?php

namespace MystNov\Core\Models;

use MystNov\Core\Enums\PointOrderPaymentMethod;
use MystNov\Core\Enums\WalletSource;
use MystNov\Core\Enums\WalletTransactionType;

class Wallet extends Model
{
    protected $connection = 'mysql_main';

    protected $table = 'wallets';

    protected $fillable = [
        'page_id',
        'parent_id',
        'owner_id',
        'owner_type',
        'point',
        'balance',
        'source',
        'morph_member_id',
        'morph_type',
        'morph_id',
    ];

    public function getSourceLabelAttribute()
    {
        return WalletSource::options()[$this->source];
    }

    public function getTypeLabelAttribute()
    {
        return WalletTransactionType::options()[$this->morph_type];
    }

    public function getTargetUrlAttribute()
    {
        if (is_admin()) {
            // Admin nhận point/chia hoa hồng từ giao dịch mua Package của member
            // Admin nhận point từ giao dịch mua License của member => TH này không tồn tại
            if ($this->morph_type == WalletTransactionType::ORDER->value) {
                return route('order.show', $this->morph_id);
            }

            // Admin nhận point từ giao dịch nạp tiền của member
            if ($this->morph_type == WalletTransactionType::POINT_ORDER->value) {
                return route('point-order.show', $this->morph_id);
            }
        }

        if (is_master()) {
            // Master IB nhận point/chia hoa hồng từ giao dịch mua License của member
            if ($this->morph_type == WalletTransactionType::ORDER->value) {
                return route('master.order.show', $this->morph_id);
            }
        }

        // Member trừ point sau khi mua Package trên System
        // Member trừ point sau khi mua License trên Master Page
        if ($this->morph_type == WalletTransactionType::ORDER->value) {
            return route('member.orders.detail', $this->target_label);
        }

        // Member nhận hoa hồng từ một member thuộc network
        // if($this->morph_type == WalletTransactionType::NETWORK_MEMBER->value)
        //     return 'mailto:' . $this->network_member->email;

        return;
    }

    public function getTargetLabelAttribute()
    {
        if (is_admin()) {
            // System Wallet: Mã đơn hàng Package khi member mua Package trên System
            // System Wallet: Mã đơn hàng License khi member mua License trên Master Page
            if ($this->morph_type == WalletTransactionType::ORDER->value) {
                return $this->product_order->code;
            }

            // System Wallet: Mã đơn hàng Nạp point khi member nạp point
            if ($this->morph_type == WalletTransactionType::POINT_ORDER->value) {
                return $this->point_order->code;
            }
        }

        if (is_master()) {
            // Master Wallet: Mã đơn hàng License khi member mua License trên Master Page
            if ($this->morph_type == WalletTransactionType::ORDER->value) {
                return $this->product_order->code;
            }
        }

        // Member Wallet: Mã đơn hàng khi member mua Package trên System
        // Member Wallet: Mã đơn hàng khi member mua License trên Master Page
        if ($this->morph_type == WalletTransactionType::ORDER->value) {
            return $this->product_order->code;
        }

        // Member Wallet: Member nạp point
        if ($this->source == WalletSource::RECHARGE->value && $this->morph_type == WalletTransactionType::POINT_ORDER->value) {
            return PointOrderPaymentMethod::options()[$this->point_order->payment_method];
        }

        // Member Wallet: Member nhận hoa hồng từ một member thuộc network
        if ($this->morph_type == WalletTransactionType::NETWORK_MEMBER->value) {
            return $this->network_member->name . '[' . $this->network_member->email . ']';
        }

        return;
    }

    public function order()
    {
        if ($this->morph_type == WalletTransactionType::ORDER->value) {
            return $this->hasOne(Order::class, 'id', 'morph_id');
        }

        if ($this->morph_type == WalletTransactionType::POINT_ORDER->value) {
            return $this->hasOne(PointOrder::class, 'id', 'morph_id');
        }

        return;
    }

    public function product_order()
    {
        return $this->hasOne(Order::class, 'id', 'morph_id');
    }

    public function point_order()
    {
        if ($this->morph_type == WalletTransactionType::POINT_ORDER->value) {
            return $this->hasOne(PointOrder::class, 'id', 'morph_id');
        }
    }

    public function network_member()
    {
        if ($this->morph_type == WalletTransactionType::NETWORK_MEMBER->value) {
            return $this->hasOne(Member::class, 'id', 'morph_id');
        }
    }

    public function sub_transactions()
    {
        return $this->hasMany(Wallet::class, 'parent_id', 'id');
    }

    public function insert($data)
    {
        $currentBalance = self::where('owner_id', $data->owner_id ?? null)->where('owner_type', $data->owner_type)->desc()->first()->balance ?? 0;

        $rec = new self;
        $rec->owner_id = $data->owner_id ?? null;
        $rec->owner_type = $data->owner_type;
        $rec->page_id = $data->page_id ?? null;
        $rec->parent_id = $data->parent_id ?? null;
        $rec->point = $data->point;
        $rec->source = $data->source;
        $rec->morph_member_id = $data->morph_member_id ?? null;
        $rec->morph_type = $data->morph_type ?? null;
        $rec->morph_id = $data->morph_id ?? null;
        $rec->balance = $currentBalance + $data->point;
        $rec->save();

        return $rec;
    }

    public function getBalance($ownerType, $ownerId = null)
    {
        return $this->where('owner_id', $ownerId)->where('owner_type', $ownerType)->desc()->first()->balance ?? 0;
    }

    public static function create(array $attributes = [])
    {
        $balance = self::where('owner_id', $attributes['owner_id'] ?? null)->where('owner_type', $attributes['owner_type'])->desc()->first()->balance ?? 0;

        $attributes['balance'] = $balance + $attributes['point'];

        return static::query()->create($attributes);
    }

}
