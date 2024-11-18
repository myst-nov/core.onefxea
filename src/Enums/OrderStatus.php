<?php

namespace MystNov\Core\Enums;

enum OrderStatus: string
{
    case ORDERED = 'ordered';
    case CUSTOMER_PAID = 'customer_paid';
    case PAID = 'paid';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case FAILED = 'failed';

    public function label(): string
    {
        return match($this) {
            self::ORDERED       => 'Ordered',
            self::CUSTOMER_PAID => 'Customer Paid',
            self::PAID          => 'Paid',
            self::PROCESSING    => 'Processing',
            self::COMPLETED     => 'Completed',
            self::CANCELLED     => 'Cancelled',
            self::FAILED        => 'Failed',
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
