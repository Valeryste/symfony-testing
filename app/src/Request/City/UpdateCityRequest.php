<?php

namespace App\Request\City;

use App\Request\BaseRequest;
use App\Validator\Constraints\ValidCountry;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

class UpdateCityRequest extends BaseRequest
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
    protected ?string $name = null;

    #[Type(type: 'integer', message: 'Country must be an integer')]
    #[Positive(message: 'country ID must be a positive number')]
    #[ValidCountry]
    protected ?int $countryId = null;

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'countryId' => $this->countryId
        ];
    }
}