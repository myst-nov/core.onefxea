<?php

namespace MystNov\Core\Enums;

enum CoinPaymentsStatus: string
{
    case CREATED = 'created';
    case WAIT_FOR_FUNDS = 'wait_for_funds';
    case FUNDS_RECEIVED = 'funds_received';
    case COMPLETE = 'complete';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::CREATED        => 'Created',
            self::WAIT_FOR_FUNDS => 'Waiting For Funds',
            self::FUNDS_RECEIVED => 'Funds Received',
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
