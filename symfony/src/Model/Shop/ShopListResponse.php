<?php

namespace App\Model\Shop;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class ShopListResponse
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public readonly int $currentPage,

        #[OA\Property(type: 'integer', example: 11)]
        public readonly int $totalCount,

        #[OA\Property(
            property: 'shops',
            type: 'array',
            items: new OA\Items(ref: new Model(type: ShopResponse::class))
        )]
        public readonly array $shops
    ) {
    }

    public static function fromPagination(PaginationInterface $pagination): self
    {
        return new self(
            currentPage: $pagination->getCurrentPageNumber(),
            totalCount: $pagination->getTotalItemCount(),
            shops: array_map(
                function ($shop) {
                    return ShopResponse::fromEntity($shop);
                },
                $pagination->getItems()
            )
        );
    }
}