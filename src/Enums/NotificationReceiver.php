<?php

namespace MystNov\Core\Enums;

enum NotificationReceiver: string
{
    case MEMBER = 'MEMBER';
    case MASTER = 'MASTER';
    case SYSTEM = 'SYSTEM';
}
