<?php

namespace App\DTO\Permission;

use App\DTO\BaseDTO;

class StoreDTO extends BaseDTO
{
    public function __construct(
        public readonly string $route,
        public readonly int $role_id
    ) {
    }
}
