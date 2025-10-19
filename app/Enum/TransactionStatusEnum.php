<?php

namespace App\Enum;

enum TransactionStatusEnum: string
{
    case PENDING = 'pending';
    case SETTLEMENT = 'settlement';
    case EXPIRE = 'expire';
    case CANCEL = 'cancel';
    case FAILURE = 'failure';
}
