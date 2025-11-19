<?php

namespace App\Model\City;

use App\Entity\Country;
use App\Model\Country\CountryResponse;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class CityResponse
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public readonly int $id,

        #[OA\Property(type: 'string', example: 'Grodno')]
        public readonly string $name,

        #[OA\Property(
            property: 'country',
            ref: new Model(type: CountryResponse::class)
        )]
        public readonly CountryResponse $country,

        #[OA\Property(type: 'string', format: 'date-time', example: '2025-11-12T06:39:22+00:00')]
        public readonly \DateTime $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', example: '2025-11-12T06:39:22+00:00|null', nullable: true)]
        public readonly ?\DateTime $updatedAt
    ) {
    }
}