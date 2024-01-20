<?php

namespace App\Enum;

enum PaymentStatusEnum: string
{
    case PENDING = 'pending';
    case COMPLETE = 'complete';
    case FAILED = 'failed';
}
