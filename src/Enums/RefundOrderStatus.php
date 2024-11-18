<?php

namespace MystNov\Core\Enums;

enum RefundOrderStatus: string
{
    case REQUESTED = 'requested';
    case COMPLETED = 'completed';
    case REFUSED = 'refused';

    public function label(): string
    {
        return match($this) {
            self::REQUESTED => 'Requested',
            self::COMPLETED => 'Completed',
            self::REFUSED   => 'Refused',
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
