<?php

namespace App\Model\User;

use Knp\Component\Pager\Pagination\PaginationInterface;
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

    public static function fromPagination(PaginationInterface $pagination): self
    {
        return new self(
            currentPage: $pagination->getCurrentPageNumber(),
            totalCount: $pagination->getTotalItemCount(),
            users: array_map(
                function ($user) {
                    return UserResponse::fromEntity($user);
                },
                $pagination->getItems()
            )
        );
    }
}