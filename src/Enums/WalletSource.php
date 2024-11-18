<?php

namespace MystNov\Core\Enums;

enum WalletSource: string
{
    case ORDER = 'order'; // Member Wallet
    case GET_FROM_ORDER = 'get_from_order'; // Master Wallet

    case GET_COMMISSION = 'get_commission'; // Member Wallet
    case SEND_COMMISSION = 'send_commission'; // System & Master Wallet

    case GET_REFUND = 'get_refund'; // Member Wallet
    case SEND_REFUND = 'send_refund'; // System & Master Wallet => REMOVE

    case GET_TRANSFER = 'get_transfer'; // Member Wallet
    case SEND_TRANSFER = 'send_transfer'; // Master Wallet

    case RECHARGE = 'recharge'; // Member Wallet

    case GET_SHARES = 'get_shares'; // Member Wallet
    case SEND_SHARES_TO_FOUNDER = 'send_shares_to_founder'; // System Wallet

    case WITHDRAW = 'withdraw';

    public function label(): string
    {
        return match($this) {
            self::ORDER          => 'Member Order',
            self::GET_FROM_ORDER => 'Get Point From Order',

            self::GET_COMMISSION  => 'Receive Commission Points From Network\'s Member',
            self::SEND_COMMISSION => 'Share Commission To Network\'s Member',

            self::GET_REFUND  => 'Refund From Order',
            self::SEND_REFUND => 'Refund For Member From Order',

            self::GET_TRANSFER  => 'Receive Transferred Points',
            self::SEND_TRANSFER => 'Transfer Points To Member Wallet',

            self::RECHARGE => 'Recharge Point To Member Wallet',

            self::GET_SHARES             => 'Get Shares From System',
            self::SEND_SHARES_TO_FOUNDER => 'Send Shares To Founder',

            self::WITHDRAW => 'Withdraw',
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
