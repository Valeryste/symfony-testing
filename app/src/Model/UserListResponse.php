<?php

namespace App\Model;

use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class UserListResponse
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public readonly int $currentPage,

        #[OA\Property(type: 'integer', example: 11)]
        public readonly int $totalCount,

        #[OA\Property(
            property: 'users',
            type: 'array',
            items: new OA\Items(ref: new Model(type: UserResponse::class))
        )]
        public readonly array $users
    ) {
    }

}