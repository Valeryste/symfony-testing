<?php

namespace App\Model\Employee;

use App\Entity\Employee;
use App\Model\Shop\ShopResponse;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class EmployeeResponse
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public readonly int $id,

        #[OA\Property(type: 'string', example: 'Earlene')]
        public readonly string $name,

        #[OA\Property(type: 'string', example: 'Bode')]
        public readonly string $surname,

        #[OA\Property(type: 'string', example: '102-401-5112')]
        public readonly string $phone,

        #[OA\Property(type: 'string', example: 'driver')]
        public readonly string $position,

        #[OA\Property(type: 'string', example: 'jjohns@wuckert.com')]
        public readonly string $email,

        #[OA\Property(type: 'bool', example: true)]
        public readonly bool $isDismissed,

        #[OA\Property(
            property: 'shop',
            ref: new Model(type: ShopResponse::class)
        )]
        public readonly ShopResponse $shop,

        #[OA\Property(type: 'string', format: 'date-time', example: '2025-11-12T06:39:22+00:00')]
        public readonly \DateTime $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', example: '2025-11-12T06:39:22+00:00|null', nullable: true)]
        public readonly ?\DateTime $updatedAt
    ) {
    }

    public static function fromEntity(Employee $employee): self
    {
        return new self(
            id: $employee->getId(),
            name: $employee->getName(),
            surname: $employee->getSurname(),
            phone: $employee->getPhone(),
            position: $employee->getPosition(),
            email: $employee->getEmail(),
            isDismissed: $employee->isDismissed(),
            shop: ShopResponse::fromEntity($employee->getShop()),
            createdAt: $employee->getCreatedAt(),
            updatedAt: $employee->getUpdatedAt()
        );
    }
}