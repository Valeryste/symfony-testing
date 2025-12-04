<?php

namespace App\Model\City;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class CityListResponse
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public readonly int $currentPage,

        #[OA\Property(type: 'integer', example: 11)]
        public readonly int $totalCount,

        #[OA\Property(
            property: 'cities',
            type: 'array',
            items: new OA\Items(ref: new Model(type: CityResponse::class))
        )]
        public readonly array $cities
    ) {
    }

    public static function fromPagination(PaginationInterface $pagination): self
    {
        return new self(
            currentPage: $pagination->getCurrentPageNumber(),
            totalCount: $pagination->getTotalItemCount(),
            cities: array_map(
                function ($city) {
                    return CityResponse::fromEntity($city);
                },
                $pagination->getItems()
            )
        );
    }
}