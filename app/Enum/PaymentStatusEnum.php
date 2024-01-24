<?php

namespace App\Enum;

enum PaymentStatusEnum: string
{
    case PENDING = 'pending';
    case COMPLETE = 'complete';
    case FAILED = 'failed';
    case DOWN_PAYMENT = 'down_payment';
}
