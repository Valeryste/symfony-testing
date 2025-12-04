<?php

namespace App\DTO\Api\Admin\Category;

final class StoreCategoryDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?int $parentId
    ) {
    }
}