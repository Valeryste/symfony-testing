<?php

namespace App\Model;

use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class CountryListResponse
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public readonly int $currentPage,

        #[OA\Property(type: 'integer', example: 11)]
        public readonly int $totalCount,

        #[OA\Property(
            property: 'countries',
            type: 'array',
            items: new OA\Items(ref: new Model(type: CountryResponse::class))
        )]
        public readonly array $countries
    ) {
    }
}