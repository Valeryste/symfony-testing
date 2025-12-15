<?php

namespace App\Model\PriceHistory;

use App\Entity\PriceHistory;
use OpenApi\Attributes as OA;

class PriceHistoryResponse
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public readonly int $id,

        #[OA\Property(type: 'float', example: 19793.20)]
        public readonly float $price,

        #[OA\Property(type: 'string', format: 'date-time', example: '2025-11-12T06:39:22+00:00')]
        public readonly \DateTime $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', example: '2025-11-12T06:39:22+00:00|null', nullable: true)]
        public readonly ?\DateTime $updatedAt
    ) {
    }

    public static function fromEntity(PriceHistory $priceHistory): self
    {
        return new self(
            id: $priceHistory->getId(),
            price: $priceHistory->getPrice(),
            createdAt: $priceHistory->getCreatedAt(),
            updatedAt: $priceHistory->getUpdatedAt()
        );
    }
}