<?php

namespace App\Request\Country;

use App\Entity\Country;
use App\Request\BaseRequest;
use App\Validator\Constraints\UniqueEntityField;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

class UpdateCountryRequest extends BaseRequest
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
        message: 'name can only contain letters, numbers, and symbols . _ - between characters.
         It must start and end with a letter or number.'
    )]
    #[UniqueEntityField(
        entityClass: Country::class,
        field: 'name',
        message: 'There is already an country with this username'
    )]
    protected ?string $name = null;

    public function getName(): ?string
    {
        return $this->name;
    }

}