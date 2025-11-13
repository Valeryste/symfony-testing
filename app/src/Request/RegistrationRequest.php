<?php

namespace App\Request;

use App\Entity\User;
use App\Validator\Constraints\UniqueEntityField;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

class RegistrationRequest extends BaseRequest
{
    #[Type(type: 'string', message: 'Type should be string')]
    #[NotBlank(message: 'field username is required')]
    #[Length(
        min: 3,
        max: 255,
        minMessage: 'Username must be at least 3 characters',
        maxMessage: 'Username cannot be longer than 255 characters'
    )]
    #[Regex(
        pattern: '/^[a-zA-Z0-9]+([_.-]?[a-zA-Z0-9])*$/',
        message: 'Username can only contain letters, numbers, and symbols . _ - between characters.
         It must start and end with a letter or number.'
    )]
    #[UniqueEntityField(
        entityClass: User::class,
        field: 'username',
        message: 'There is already an account with this username'
    )]
    protected string $username;

    #[Type(type: 'string', message: 'Type should be string')]
    #[NotBlank(message: 'field username is required')]
    #[Length(
        min: 3,
        max: 255,
        minMessage: 'Email must be at least 3 characters',
        maxMessage: 'Email cannot be longer than 255 characters'
    )]
    #[Email]
    #[UniqueEntityField(
        entityClass: User::class,
        field: 'email',
        message: 'There is already an account with this email'
    )]
    protected string $email;

    #[Type(type: 'string', message: 'Type should be string')]
    #[NotBlank(message: 'field password is required')]
    #[Length(
        min: 6,
        max: 255,
        minMessage: 'Password must be at least 6 characters',
        maxMessage: 'Password cannot be longer than 255 characters'
    )]
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