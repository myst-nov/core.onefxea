<?php

namespace MystNov\Core\Enums;

enum PointOrderSource: string
{
    case RECHARGE = 'recharge';
    case COMMISSION = 'commission';
    case REFUND_ORDER = 'refund_order';
    case PAY_FOR_ORDER = 'pay_for_order';

    public function label(): string
    {
        return match($this) {
            self::RECHARGE      => 'Recharge Point into Wallet',
            self::COMMISSION    => 'Receive commission through Network Member',
            self::REFUND_ORDER  => 'Refund by Order',
            self::PAY_FOR_ORDER => 'Pay for Order',
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
