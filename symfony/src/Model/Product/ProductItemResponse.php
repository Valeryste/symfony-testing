<?php

namespace App\Model\Product;

use App\Entity\Product;
use OpenApi\Attributes as OA;

class ProductItemResponse
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public readonly int $id,

        #[OA\Property(type: 'string', example: 'Qui minima')]
        public readonly string $name,

        #[OA\Property(type: 'float', example: 19793.20)]
        public readonly float $price
    ) {
    }

    public static function fromEntity(Product $product): self
    {
        return new self(
            id: $product->getId(),
            name: $product->getName(),
            price: $product->getPrice()
        );
    }
}