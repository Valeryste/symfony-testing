<?php

namespace App\Model\Country;

use App\Entity\Country;
use OpenApi\Attributes as OA;

class CountryResponse
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public readonly int $id,

        #[OA\Property(type: 'string', example: 'Italy')]
        public readonly string $name,

        #[OA\Property(type: 'string', format: 'date-time', example: '2025-11-12T06:39:22+00:00')]
        public readonly \DateTime $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', example: '2025-11-12T06:39:22+00:00|null', nullable: true)]
        public readonly ?\DateTime $updatedAt
    ) {
    }

    public static function fromEntity(Country $country): static
    {
        return new static(
            id: $country->getId(),
            name: $country->getName(),
            createdAt: $country->getCreatedAt(),
            updatedAt: $country->getUpdatedAt()
        );
    }
}