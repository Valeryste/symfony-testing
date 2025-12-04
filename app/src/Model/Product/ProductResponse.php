<?php

namespace App\Model\Product;

use App\Entity\Product;
use App\Model\Category\CategoryItemResponse;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class ProductResponse
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public readonly int $id,

        #[OA\Property(type: 'string', example: 'Qui minima')]
        public readonly string $name,

        #[OA\Property(type: 'float', example: 19793.20)]
        public readonly float $price,

        #[OA\Property(type: 'int', example: 8559)]
        public readonly int $count,

        #[OA\Property(
            property: 'categories',
            type: 'array',
            items: new OA\Items(ref: new Model(type: CategoryItemResponse::class))
        )]
        public readonly array $categories,

        #[OA\Property(type: 'bool', example: true)]
        public readonly bool $isActive,

        #[OA\Property(type: 'string', format: 'date-time', example: '2025-11-12T06:39:22+00:00')]
        public readonly \DateTime $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', example: '2025-11-12T06:39:22+00:00|null', nullable: true)]
        public readonly ?\DateTime $updatedAt
    ) {
    }

    public static function fromEntity(Product $product): static
    {
        return new static(
            id: $product->getId(),
            name: $product->getName(),
            price: $product->getPrice(),
            count: $product->getCount(),
            categories: $product->getCategories()->map(function ($child) {
                return CategoryItemResponse::fromEntity($child);
            })->getValues(),
            isActive: $product->isActive(),
            createdAt: $product->getCreatedAt(),
            updatedAt: $product->getUpdatedAt()
        );
    }
}