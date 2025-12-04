<?php

namespace App\DTO\Api\Admin\Category;

final class UpdateCategoryDTO
{
    public function __construct(
        public readonly ?string $name,
        public readonly ?int $parentId,
        public readonly ?bool $isActive
    ) {
    }
}