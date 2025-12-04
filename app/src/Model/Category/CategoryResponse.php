<?php

namespace App\Model\Category;

use App\Entity\Category;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class CategoryResponse
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public readonly int $id,

        #[OA\Property(type: 'string', example: 'Qui minima')]
        public readonly string $name,

        #[OA\Property(
            property: 'parent',
            ref: new Model(type: CategoryItemResponse::class)
        )]
        public readonly ?CategoryItemResponse $parent,

        #[OA\Property(
            property: 'children',
            type: 'array',
            items: new OA\Items(ref: new Model(type: CategoryItemResponse::class))
        )]
        public readonly ?array $children,

        #[OA\Property(type: 'bool', example: true)]
        public readonly bool $isActive,

        #[OA\Property(type: 'string', format: 'date-time', example: '2025-11-12T06:39:22+00:00')]
        public readonly \DateTime $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', example: '2025-11-12T06:39:22+00:00|null', nullable: true)]
        public readonly ?\DateTime $updatedAt
    ) {
    }

    public static function fromEntity(Category $category): static
    {
        return new static(
            id: $category->getId(),
            name: $category->getName(),
            parent: $category->getParent() ? CategoryItemResponse::fromEntity($category->getParent()) : null,
            children: $category->getChildren()->map(function ($child) {
                return CategoryItemResponse::fromEntity($child);
            })->getValues(),
            isActive: $category->isActive(),
            createdAt: $category->getCreatedAt(),
            updatedAt: $category->getUpdatedAt()
        );
    }
}