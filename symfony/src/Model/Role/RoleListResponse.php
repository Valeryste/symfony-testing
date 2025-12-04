<?php

namespace App\Model\Role;

use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class RoleListResponse
{
    public function __construct(
        #[OA\Property(
            property: 'roles',
            type: 'array',
            items: new OA\Items(ref: new Model(type: RoleResponse::class))
        )]
        public readonly array $roles
    ) {
    }
}