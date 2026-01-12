<?php

namespace App\DTO\Authentication;

use App\DTO\BaseDTO;

class RegisterDTO extends BaseDTO
{
    public function __construct(
        public readonly string $username,
        public readonly string $email,
        public readonly string $password,
        public readonly bool $is_active = true
    ) {
    }
}
