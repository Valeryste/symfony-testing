<?php

namespace App\Model\Role;

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
}