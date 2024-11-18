<?php

namespace MystNov\Core\Enums;

enum WithdrawStatus: string
{
    case REQUESTED = 'requested';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case REFUSED = 'refused';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::REQUESTED  => 'Requested',
            self::PROCESSING => 'Processing',
            self::COMPLETED  => 'Completed',
            self::REFUSED    => 'Refused',
            self::CANCELLED  => 'Cancelled',
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
