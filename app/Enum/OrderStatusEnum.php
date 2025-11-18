<?php

namespace App\Enum;

enum OrderStatusEnum: string
{
    case PENDING = 'pending';
    case SETTLEMENT = 'settlement';
    case EXPIRE = 'expire';
    case CANCEL = 'cancel';
    case DENY = 'DENY';
    case PROSES = 'proses';
}
