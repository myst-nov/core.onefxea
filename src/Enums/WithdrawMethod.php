<?php

namespace MystNov\Core\Enums;

enum WithdrawMethod: string
{
    case BINANCE_PAY = 'binance_pay';
    case PAYPAL = 'paypal';

    public function label(): string
    {
        return match($this) {
            self::BINANCE_PAY => 'Binance Pay',
            self::PAYPAL      => 'PayPal',
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
