<?php

namespace MystNov\Core\Enums;

enum ProductType: string
{
    case VPS = 'vps';
    case LICENSE = 'license';

    public function label(): string
    {
        return match($this) {
            self::VPS     => 'VPS',
            self::LICENSE => 'License',
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
