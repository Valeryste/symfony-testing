<?php

namespace App\Request\Employee;

use App\Request\BaseRequest;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

class UpdateEmployeeRequest extends BaseRequest
{
    #[Type(type: 'string', message: 'Type should be string')]
    #[Length(
        min: 3,
        max: 255,
        minMessage: 'name must be at least 3 characters',
        maxMessage: 'name cannot be longer than 255 characters'
    )]
    #[Regex(
        pattern: '/^[a-zA-Zа-яА-Я0-9_]+( [a-zA-Zа-яА-Я0-9_]+)*$/u',
        message: 'Name can contain letters, numbers, underscores with single spaces between words'
    )]
    protected ?string $name = null;

    #[Type(type: 'string', message: 'Type should be string')]
    #[Length(
        min: 3,
        max: 255,
        minMessage: 'surname must be at least 3 characters',
        maxMessage: 'surname cannot be longer than 255 characters'
    )]
    #[Regex(
        pattern: '/^[a-zA-Zа-яА-Я0-9_]+( [a-zA-Zа-яА-Я0-9_]+)*$/u',
        message: 'surname can contain letters, numbers, underscores with single spaces between words'
    )]
    protected ?string $surname = null;

    #[Type(type: 'string', message: 'Type should be string')]
    #[Length(
        min: 10,
        max: 15,
        minMessage: 'Phone number must be at least 10 digits',
        maxMessage: 'Phone number must be no more 15 digits'
    )]
    #[Regex(
        pattern: '/^\+?[0-9\s\-\(\)]+$/',
        message: 'Phone number can only contain digits, spaces, hyphens and parentheses'
    )]
    protected ?string $phone = null;

    #[Type(type: 'string', message: 'Type should be string')]
    #[Length(
        min: 3,
        max: 255,
        minMessage: 'Email must be at least 3 characters',
        maxMessage: 'Email cannot be longer than 255 characters'
    )]
    #[Email]
    protected ?string $email = null;

    #[Type(type: 'string', message: 'Type should be string')]
    #[Length(
        min: 3,
        max: 255,
        minMessage: 'position must be at least 3 characters',
        maxMessage: 'position cannot be longer than 255 characters'
    )]
    #[Regex(
        pattern: '/^[a-zA-Zа-яА-Я0-9_]+( [a-zA-Zа-яА-Я0-9_]+)*$/u',
        message: 'Position can contain letters, numbers, underscores with single spaces between words'
    )]
    protected ?string $position = null;

    #[Type(type: 'integer', message: 'City must be an integer')]
    #[Positive(message: 'city ID must be a positive number')]
    protected ?int $shopId = null;

    #[Type(type: 'bool', message: 'Dismissed must be true or false')]
    protected ?bool $isDismissed = true;

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'surname' => $this->surname,
            'phone' => $this->phone,
            'email' => $this->email,
            'position' => $this->position,
            'shopId' => $this->shopId,
            'isDismissed' => $this->isDismissed
        ];
    }
}