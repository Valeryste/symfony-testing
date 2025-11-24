<?php

namespace App\Model\Employee;

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
}