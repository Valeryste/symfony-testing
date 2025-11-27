<?php

namespace App\Request\Product;

use App\Request\BaseRequest;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

class UpdateProductRequest extends BaseRequest
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

    #[Type(type: 'int', message: 'Type count should be integer')]
    #[Range(
        notInRangeMessage: 'count must be at least least 0 and cannot be longer than 4,294,967,295',
        min: 0,
        max: 4_294_967_295
    )]
    protected ?int $count  = null;

    #[Type(type: 'float', message: 'The value is not a valid decimal number')]
    #[Range(
        notInRangeMessage: 'count must be at least least 0 and cannot be longer than 4,294,967,295',
        min: 0,
        max: 4_294_967_295
    )]
    protected ?float $price = null;

    #[Type(type: 'bool', message: 'Active must be true или false')]
    protected bool $isActive = true;

    #[Type(type: 'array', message: 'categoryIds must be an array')]
    #[All([
        new Type(type: 'integer', message: 'Each category ID must be an integer'),
        new Positive(message: 'Each category ID must be a positive integer')
    ])]
    protected ?array $categoryIds = null;

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'count' => $this->count,
            'price' => $this->price,
            'isActive' => $this->isActive,
            'categoryIds' => $this->categoryIds
        ];
    }
}