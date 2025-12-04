<?php

namespace App\Model\Role;

use App\Entity\Role;
use OpenApi\Attributes as OA;

class RoleResponse
{
    public function __construct(
        #[OA\Property(property: 'id', type: 'integer', example: 1)]
        public readonly int  $id,

        #[OA\Property(property: 'name', type: 'string', example: 'ADMIN')]
        public readonly string $name,
    ) {
    }

    public static function fromEntity(Role $role): self
    {
        return new self(
            id: $role->getId(),
            name: $role->getName()
        );
    }
}