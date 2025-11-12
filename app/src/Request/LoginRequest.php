<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

class LoginRequest extends BaseRequest
{
    #[Type(type: 'string')]
    #[NotBlank]
    #[Regex(pattern: '/^[a-zA-Z0-9]+([_.-]?[a-zA-Z0-9])*$/')]
    protected string $username;

    #[Type(type: 'string')]
    #[NotBlank]
    protected string $password;

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

}