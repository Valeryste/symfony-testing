<?php

namespace App\Model\Category;

use OpenApi\Attributes as OA;

class CategoryItemResponse
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public readonly int $id,

        #[OA\Property(type: 'string', example: 'Категория voluptatem')]
        public readonly string $name,

        #[OA\Property(type: 'bool', example: true)]
        public readonly bool $isActive,
    ) {
    }
}