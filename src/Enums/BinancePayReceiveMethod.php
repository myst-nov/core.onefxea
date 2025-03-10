<?php

namespace MystNov\Core\Enums;

enum BinancePayReceiveMethod: string
{
    case EMAIL = 'email';
    case BINANCE_ID = 'binanceId';

    public function label(): string
    {
        return match($this) {
            self::EMAIL      => 'Email / Phone',
            self::BINANCE_ID => 'Binance ID',
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
