<?php

namespace App\Enums;

enum StatusEnum: string
{
    case NEW = 'NEW';

    case CONFIRMED = 'CONFIRMED';

    case PROCESSING = 'PROCESSING';

    case SHIPPED = 'SHIPPED';

    case COMPLETED = 'COMPLETED';
}
