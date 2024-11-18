<?php

namespace MystNov\Core\Enums;

enum WalletTransactionType: string
{
    case ORDER = 'order'; // Member Wallet
    case POINT_ORDER = 'point_order'; // Member Wallet
    case NETWORK_MEMBER = 'network_member'; // Member Wallet
    case MASTER_WALLET = 'master_wallet'; // Member Wallet
    case SHAREHOLDING_TRANSACTION = 'shareholding_transaction'; // Member Wallet
    case WITHDRAW_REQUEST = 'withdraw_request';

    public function label(): string
    {
        return match($this) {
            self::ORDER                    => 'From Order',
            self::POINT_ORDER              => 'Via',
            self::NETWORK_MEMBER           => 'From Member',
            self::MASTER_WALLET            => 'From Master Wallet To Member Wallet',
            self::SHAREHOLDING_TRANSACTION => 'From Shareholding Transaction',
            self::WITHDRAW_REQUEST         => 'From Withdraw Request',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $enum) => [
            $enum->value => $enum->label(),
        ])
            ->toArray();
    }
}
