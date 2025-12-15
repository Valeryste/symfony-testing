<?php

namespace App\Model\Employee;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class EmployeeListResponse
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public readonly int $currentPage,

        #[OA\Property(type: 'integer', example: 11)]
        public readonly int $totalCount,

        #[OA\Property(
            property: 'employees',
            type: 'array',
            items: new OA\Items(ref: new Model(type: EmployeeResponse::class))
        )]
        public readonly array $employees
    ) {
    }

    public static function fromPagination(PaginationInterface $pagination): self
    {
        return new self(
            currentPage: $pagination->getCurrentPageNumber(),
            totalCount: $pagination->getTotalItemCount(),
            employees: array_map(
                function ($employee) {
                    return EmployeeResponse::fromEntity($employee);
                },
                $pagination->getItems()
            )
        );
    }
}