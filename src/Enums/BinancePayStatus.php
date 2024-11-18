<?php

namespace MystNov\Core\Enums;

enum BinancePayStatus: string
{
    case WAIT_FOR_FUNDS = 'wait_for_funds';
    case COMPLETE = 'complete';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::WAIT_FOR_FUNDS => 'Waiting For Funds',
            self::COMPLETE       => 'Complete',
            self::CANCELLED      => 'Cancelled / Timed Out',
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
