<?php

namespace App\Request\User;

use App\Entity\User;
use App\Request\BaseRequest;
use App\Validator\Constraints\UniqueEntityField;
use App\Validator\Constraints\ValidRole;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

class UpdateUserRequest extends BaseRequest
{
    #[Type(type: 'string', message: 'Type should be string')]
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
    protected ?string $username = null;


    #[Type(type: 'string', message: 'Type should be string')]
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
    protected ?string $email = null;

    #[Type(type: 'bool', message: 'Active must be true или false')]
    protected bool $isActive = true;

    #[Type(type: 'integer', message: 'Role must be an integer')]
    #[Positive(message: 'role ID must be a positive number')]
    #[ValidRole]
    protected ?int $roleId = null;

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getRoleId(): ?int
    {
        return $this->roleId;
    }

    public function toArray(): array
    {
        return [
            'username' => $this->username,
            'email' => $this->email,
            'isActive' => $this->isActive,
            'roleId' => $this->roleId
        ];
    }
}