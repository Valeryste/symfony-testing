<?php

namespace App\Enum;

enum Role: string
{
    case MANAGER = 'MANAGER';

    case ADMIN = 'ADMIN';

    case USER = 'USER';
}
