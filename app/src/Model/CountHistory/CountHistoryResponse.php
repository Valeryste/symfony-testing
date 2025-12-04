<?php

namespace App\Model\CountHistory;

use App\Entity\CountHistory;
use OpenApi\Attributes as OA;

class CountHistoryResponse
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public readonly int  $id,

        #[OA\Property(type: 'integer', example: 11)]
        public readonly int  $count,

        #[OA\Property(type: 'string', format: 'date-time', example: '2025-11-12T06:39:22+00:00')]
        public readonly \DateTime $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', example: '2025-11-12T06:39:22+00:00|null', nullable: true)]
        public readonly ?\DateTime $updatedAt
    ) {
    }

    public static function fromEntity(CountHistory $countHistory): self
    {
        return new self(
            id: $countHistory->getId(),
            count: $countHistory->getCount(),
            createdAt: $countHistory->getCreatedAt(),
            updatedAt: $countHistory->getUpdatedAt()
        );
    }
}