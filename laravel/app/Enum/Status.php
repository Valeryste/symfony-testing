<?php

namespace App\Enum;

enum Status: string
{
    case NEW = 'NEW';

    case CONFIRMED = 'CONFIRMED';

    case PROCESSING = 'PROCESSING';

    case SHIPPED = 'SHIPPED';

    case COMPLETED = 'COMPLETED';
}
