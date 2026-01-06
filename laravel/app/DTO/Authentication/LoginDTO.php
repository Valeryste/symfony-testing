<?php

namespace App\DTO\Authentication;

use App\DTO\BaseDTO;

class LoginDTO extends BaseDTO
{
    public function __construct(
        public readonly string $username,
        public readonly string $password,
    ) {
    }
}
