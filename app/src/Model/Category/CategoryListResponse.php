<?php

namespace App\Model\Category;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class CategoryListResponse
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public readonly int   $currentPage,

        #[OA\Property(type: 'integer', example: 11)]
        public readonly int   $totalCount,

        #[OA\Property(
            property: 'categories',
            type: 'array',
            items: new OA\Items(ref: new Model(type: CategoryItemResponse::class))
        )]
        public readonly array $categories
    ) {
    }

    public static function fromPagination(PaginationInterface $paginationCategories): self
    {
        return new self(
            currentPage: $paginationCategories->getCurrentPageNumber(),
            totalCount: $paginationCategories->getTotalItemCount(),
            categories: array_map(function ($category) {
                return CategoryItemResponse::fromEntity($category);
            }, $paginationCategories->getItems())
        );
    }
}