<?php

namespace MystNov\Core\Enums;

enum WalletOwnerType: string
{
    case MEMBER = 'member';
    case MASTER = 'master';
    case SYSTEM = 'system';
}
