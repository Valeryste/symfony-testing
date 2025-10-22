<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

class RegistrationRequest extends BaseRequest
{
    #[Type(type: 'string')]
    #[NotBlank]
    #[Length(min: 3, max: 255)]
    #[Regex(pattern: '/^[a-zA-Z0-9_]+$/')]
    protected string $username;

    #[Type(type: 'string')]
    #[NotBlank([])]
    #[Length(min: 3, max: 255)]
    #[Email]
    protected string $email;

    #[Type(type: 'string')]
    #[NotBlank([])]
    #[Length(min: 6, max: 255)]
    protected string $password;

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}