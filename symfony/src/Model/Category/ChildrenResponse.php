<?php

namespace App\Model\Category;

use App\Entity\Category;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class ChildrenResponse
{
    public function __construct(
        #[OA\Property(
            property: 'children',
            type: 'array',
            items: new OA\Items(ref: new Model(type: CategoryResponse::class))
        )]
        public readonly array $children
    ) {
    }

    public static function fromEntity(Category $category): self
    {
        return new self(
            children: $category->getChildren()->map(function ($child) {
                return CategoryResponse::fromEntity($child);
            })->getValues()
        );
    }
}