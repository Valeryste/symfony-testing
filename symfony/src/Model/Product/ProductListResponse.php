<?php

namespace App\Model\Product;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class ProductListResponse
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public readonly int   $currentPage,

        #[OA\Property(type: 'integer', example: 11)]
        public readonly int   $totalCount,

        #[OA\Property(
            property: 'products',
            type: 'array',
            items: new OA\Items(ref: new Model(type: ProductItemResponse::class))
        )]
        public readonly array $products
    ) {
    }

    public static function fromPagination(PaginationInterface $pagination): self
    {
        return new self(
            currentPage: $pagination->getCurrentPageNumber(),
            totalCount: $pagination->getTotalItemCount(),
            products: array_map(
                function ($product) {
                    return ProductItemResponse::fromEntity($product);
                },
                $pagination->getItems()
            )
        );
    }
}