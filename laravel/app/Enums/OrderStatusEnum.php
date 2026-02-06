<?php

namespace App\Enums;

enum OrderStatusEnum: string
{
    case NEW = 'NEW';

    case CONFIRMED = 'CONFIRMED';

    case PROCESSING = 'PROCESSING';

    case SHIPPED = 'SHIPPED';

    case COMPLETED = 'COMPLETED';
}
