<?php

namespace MystNov\Core\Enums;

enum NetworkSurplusPointsRecipient: string
{
    case SYSTEM = 'system';
    case MASTER_PAGE = 'master_page';

    public function label(): string
    {
        return match($this) {
            self::SYSTEM            => 'System',
            self::MASTER_PAGE       => 'Master Page',
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
