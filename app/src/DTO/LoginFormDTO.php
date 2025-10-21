<?php

namespace App\DTO;

final class LoginFormDTO extends BaseDTO
{
    public function __construct(
        public readonly string $username,
        public readonly string $password
    ){}
}