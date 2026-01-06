<?php

namespace App\Enums;

enum RoleEnum: string
{
    case MANAGER = 'MANAGER';

    case ADMIN = 'ADMIN';

    case USER = 'USER';
}
