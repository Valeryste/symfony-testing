<?php

namespace App\Request\Product;

use App\Entity\Product;
use App\Request\BaseRequest;
use App\Validator\Constraints\UniqueEntityField;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

class StoreProductRequest extends BaseRequest
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
    #[UniqueEntityField(
        entityClass: Product::class,
        field: 'name',
        message: 'Product with that name already exists'
    )]
    protected string $name;

    #[Type(type: 'int', message: 'Type count should be integer')]
    #[NotBlank(message: 'Please enter a count')]
    #[Range(
        notInRangeMessage: 'count must be at least least 0 and cannot be longer than 4,294,967,295',
        min: 0,
        max: 4_294_967_295
    )]
    protected int $count;

    #[Type(type: 'numeric', message: 'The value is not a valid decimal number')]
    #[NotBlank(message: 'Please enter a price')]
    #[Range(
        notInRangeMessage: 'Price must be between 0.01 and 99,999,999.99',
        min: 0.01,
        max: 99_999_999.99
    )]
    protected float $price;

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
            'categoryIds' => $this->categoryIds
        ];
    }
}