<?php

namespace App\Request\Category;

use App\Entity\Category;
use App\Request\BaseRequest;
use App\Validator\Constraints\UniqueEntityField;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

class UpdateCategoryRequest extends BaseRequest
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
    #[UniqueEntityField(
        entityClass: Category::class,
        field: 'name',
        message: 'Category with that name already exists'
    )]
    protected ?string $name = null;

    #[Type(type: 'int', message: 'parentId must be an int')]
    #[Positive(message: 'parentId must be a positive integer')]
    protected ?int $parentId = null;

    #[Type(type: 'bool', message: 'Active must be true или false')]
    protected bool $isActive = true;

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'parentId' => $this->parentId,
            'isActive' => $this->isActive
        ];
    }
}