<?php

namespace App\DTO\Web;

use App\DTO\BaseDTO;

final class RegisterFormDTO extends BaseDTO
{
    public function __construct(
       public readonly string $username,
       public readonly string $email,
       public readonly string $plainPassword
    ){}
}