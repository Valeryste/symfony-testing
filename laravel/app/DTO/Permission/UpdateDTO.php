<?php

namespace App\DTO\Permission;
use App\DTO\BaseDTO;

class UpdateDTO extends BaseDTO
{
    public function __construct(
       public readonly int $role_id
    ) {
    }
}
