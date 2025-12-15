<?php

namespace App\DTO\Api\Authentication;

use App\DTO\BaseDTO;

final class LoginFormDTO extends BaseDTO
{
    public function __construct(
        public readonly string $username,
        public readonly string $password
    ) {

    }
}