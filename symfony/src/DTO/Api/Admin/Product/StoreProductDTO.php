<?php

namespace App\DTO\Api\Admin\Product;

use App\DTO\BaseDTO;

final class StoreProductDTO extends BaseDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $count,
        public readonly string $price,
        public readonly ?array $categoryIds
    ) {
    }
}