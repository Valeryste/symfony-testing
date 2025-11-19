<?php

namespace App\Model\City;

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
        public readonly array $countries
    ) {
    }
}