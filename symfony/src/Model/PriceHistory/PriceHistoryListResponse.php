<?php

namespace App\Model\PriceHistory;

use App\Controller\Api\Admin\PriceHistoryController;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class PriceHistoryListResponse
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public readonly int   $currentPage,

        #[OA\Property(type: 'integer', example: 11)]
        public readonly int   $totalCount,

        #[OA\Property(
            property: 'countHistory',
            type: 'array',
            items: new OA\Items(ref: new Model(type: PriceHistoryController::class))
        )]
        public readonly array $countHistory
    ) {
    }

    public static function fromPagination(PaginationInterface $pagination): self
    {
        return new self(
            currentPage: $pagination->getCurrentPageNumber(),
            totalCount: $pagination->getTotalItemCount(),
            countHistory: array_map(
                function ($item) {
                    return PriceHistoryResponse::fromEntity($item);
                },
                $pagination->getItems()
            )
        );
    }
}