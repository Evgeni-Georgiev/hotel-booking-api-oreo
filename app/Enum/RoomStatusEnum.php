<?php

namespace App\Enum;

enum RoomStatusEnum: string
{
    case AVAILABLE = 'available';

    case OCCUPIED = 'occupied';
}
