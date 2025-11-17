<?php

namespace App\Request\Authentication;

use App\Request\BaseRequest;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

class LoginRequest extends BaseRequest
{
    #[Type(type: 'string', message: 'Type should be string')]
    #[NotBlank(message: 'field username is required')]
    #[Regex(
        pattern: '/^[a-zA-Z0-9]+([_.-]?[a-zA-Z0-9])*$/',
        message: 'Username can only contain letters, numbers, and symbols . _ - between characters.
         It must start and end with a letter or number.'
    )]
    protected string $username;

    #[Type(type: 'string', message: 'Type should be string')]
    #[NotBlank(message: 'field username is required')]
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