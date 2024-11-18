<?php

namespace MystNov\Core\Enums;

enum OrderType: string
{
    case NEW = 'new';
    case EXTEND = 'extend';
    case GIVE_AWAY = 'give_away';

    public function label(): string
    {
        return match($this) {
            self::NEW          => 'Get New',
            self::EXTEND       => 'Extend Using Date',
            self::GIVE_AWAY    => 'Give Away',
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
