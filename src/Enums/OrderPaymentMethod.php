<?php

namespace MystNov\Core\Enums;

enum OrderPaymentMethod: string
{
    case POINT = 'point';
    case MANUAL = 'manual';

    public function label(): string
    {
        return match($this) {
            self::POINT  => 'Pay with Point',
            self::MANUAL => 'Manual Transfer',
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
