<?php

namespace App\Request\Shop;

use App\Request\BaseRequest;
use App\Validator\Constraints\ValidCity;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

class StoreShopRequest extends BaseRequest
{
    #[Type(type: 'string', message: 'Type should be string')]
    #[NotBlank(message: 'field name is required')]
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
    protected string $name;

    #[Type(type: 'string', message: 'Type should be string')]
    #[NotBlank(message: 'field address is required')]
    #[Length(
        min: 3,
        max: 255,
        minMessage: 'Address must be at least 3 characters',
        maxMessage: 'Address cannot be longer than 255 characters'
    )]
    #[Regex(
        pattern: '/^[a-zA-Z0-9\s\-\.,#]+$/',
        message: 'Address can only contain letters, numbers, spaces, hyphens, commas, periods and hash symbols'
    )]
    protected string $address;

    #[Type(type: 'bool', message: 'Open must be true or false')]
    protected bool $isOpen = true;

    #[Type(type: 'integer', message: 'City must be an integer')]
    #[Positive(message: 'city ID must be a positive number')]
    #[NotBlank(message: 'cityId name is required')]
    #[ValidCity]
    protected int $cityId;

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'address' => $this->address,
            'isOpen' => $this->isOpen,
            'cityId' => $this->cityId
        ];
    }
}