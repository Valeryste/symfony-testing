<?php

namespace App\Model\Product;

use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class ProductListResponse
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public readonly int $currentPage,

        #[OA\Property(type: 'integer', example: 11)]
        public readonly int $totalCount,

        #[OA\Property(
            property: 'products',
            type: 'array',
            items: new OA\Items(ref: new Model(type: ProductItemResponse::class))
        )]
        public readonly array $products
    ) {
    }
}