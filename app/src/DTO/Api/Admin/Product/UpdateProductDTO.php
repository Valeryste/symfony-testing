<?php

namespace App\DTO\Api\Admin\Product;

use App\DTO\BaseDTO;

class UpdateProductDTO extends BaseDTO
{
    public function __construct(
        public readonly ?string $name,
        public readonly ?string $count,
        public readonly ?string $price,
        public readonly ?bool $isActive,
        public readonly ?array $categoryIds
    ) {
    }
}