<?php

namespace App\Model\User;

use App\Entity\User;
use App\Model\Role\RoleResponse;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class UserResponse
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public readonly int $id,

        #[OA\Property(type: 'string', example: 'john_doe')]
        public readonly string $username,

        #[OA\Property(type: 'string', example: 'john@example.com')]
        public readonly string $email,

        #[OA\Property(type: 'boolean', example: true)]
        public readonly bool $isActive,

        #[OA\Property(
            property: 'role',
            ref: new Model(type: RoleResponse::class)
        )]
        public readonly RoleResponse $role,

        #[OA\Property(type: 'string', format: 'date-time', example: '2025-11-12T06:39:22+00:00')]
        public readonly \DateTime $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', example: '2025-11-12T06:39:22+00:00', nullable: true)]
        public readonly ?\DateTime $updatedAt
    ) {
    }

    public static function fromEntity(User $user): self
    {
        return new self(
            id: $user->getId(),
            username: $user->getUsername(),
            email: $user->getEmail(),
            isActive: $user->isActive(),
            role: RoleResponse::fromEntity($user->getRole()),
            createdAt: $user->getCreatedAt(),
            updatedAt: $user->getUpdatedAt()
        );
    }
}