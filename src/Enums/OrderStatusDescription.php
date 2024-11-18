<?php

namespace MystNov\Core\Enums;

enum OrderStatusDescription: string
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
            self::ORDERED       => 'Order made',
            self::CUSTOMER_PAID => 'Manually paid through the Local Bank',
            self::PAID          => 'Payment confirmed',
            self::PROCESSING    => 'The technical team have received and processing',
            self::COMPLETED     => 'Order completed',
            self::CANCELLED     => 'Order cancelled',
            self::FAILED        => 'Order failed',
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
