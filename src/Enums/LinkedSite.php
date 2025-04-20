<?php

namespace MystNov\Core\Enums;

enum LinkedSite: string
{
    case EAS_ONEFXEA = 'eas.onefxea.com';
    
    public function label(): string
    {
        return match($this) {
            self::EAS_ONEFXEA=> 'https://eas.onefxea.com',
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
