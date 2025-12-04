<?php

namespace App\Model\Shop;

use App\Entity\Shop;
use App\Model\City\CityResponse;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class ShopResponse
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public readonly int $id,

        #[OA\Property(type: 'string', example: 'john_doe')]
        public readonly string $name,

        #[OA\Property(type: 'string', example: '248 Reinger LakesTreutelbury, AZ 20569')]
        public readonly string $address,

        #[OA\Property(type: 'boolean', example: true)]
        public readonly bool $isOpen,

        #[OA\Property(
            property: 'city',
            ref: new Model(type: CityResponse::class)
        )]
        public readonly CityResponse $city,

        #[OA\Property(type: 'string', format: 'date-time', example: '2025-11-12T06:39:22+00:00')]
        public readonly \DateTime $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', example: '2025-11-12T06:39:22+00:00', nullable: true)]
        public readonly ?\DateTime $updatedAt
    ) {
    }

    public static function fromEntity(Shop $shop): self
    {
        return new self(
            id: $shop->getId(),
            name: $shop->getName(),
            address: $shop->getAddress(),
            isOpen: $shop->isOpen(),
            city: CityResponse::fromEntity($shop->getCity()),
            createdAt: $shop->getCreatedAt(),
            updatedAt: $shop->getUpdatedAt()
        );
    }
}