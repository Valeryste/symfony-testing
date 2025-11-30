<?php

namespace App\Service\Api;

use App\DTO\Api\Admin\Category\StoreCategoryDTO;
use App\DTO\Api\Admin\Category\UpdateCategoryDTO;
use App\Entity\Category;
use App\Model\Category\CategoryListResponse;
use App\Model\Category\CategoryResponse;
use App\Repository\CategoryRepository;
use App\Service\BaseService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use InvalidArgumentException;

class ApiCategoryService extends BaseService
{
    private const PAGINATION_LIMIT = 10;

    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function getList(int $page, array $filters = [], array $sorts = [], array $search = []): CategoryListResponse
    {
        if ($this->hasFilter($filters, 'deletedAt', 1)) {
            $this->entityManager->getFilters()->disable('softdeleteable');
        }

        $paginationCategories = $this->categoryRepository->getPaginatedResults(
            page: $page,
            limit: self::PAGINATION_LIMIT,
            filters: $filters,
            sorts: $sorts,
            search: $search
        );

        if ($this->hasFilter($filters, 'deletedAt', 1)) {
            $this->entityManager->getFilters()->enable('softdeleteable');
        }

        return CategoryListResponse::fromPagination($paginationCategories);
    }

    public function show(Category $category): CategoryResponse
    {
        return CategoryResponse::fromEntity($category);
    }

    public function store(StoreCategoryDTO $storeCategoryDTO): CategoryResponse
    {
        $category = new Category();

        $category->setName($storeCategoryDTO->name);

        if (!empty($storeCategoryDTO->parentId)) {
            $this->setParentCategory($category, $storeCategoryDTO->parentId);
        }

        $this->entityManager->persist($category);

        $this->entityManager->flush();

        return CategoryResponse::fromEntity($category);
    }

    public function update(UpdateCategoryDTO $updateCategoryDTO, Category $category): CategoryResponse
    {
        $category->setName($updateCategoryDTO->name ?? $category->getName())
            ->setIsActive($updateCategoryDTO->isActive ?? $category->isActive())
            ->setUpdatedAt(new \DateTime());

        if (!empty($updateCategoryDTO->parentId)) {
            $this->setParentCategory($category, $updateCategoryDTO->parentId);
        }

        $this->entityManager->flush();

        return CategoryResponse::fromEntity($category);
    }

    public function delete(Category $category): void
    {
        $category->setIsActive(false);

        $this->entityManager->flush();

        $this->entityManager->remove($category);

        $this->entityManager->flush();
    }

    public function getChildren(Category $category): array
    {
        $children = $category->getChildren();
        return $children->map(
            fn($child) : CategoryResponse => CategoryResponse::fromEntity($child)
        )->toArray();
    }

    /**
     * @throws EntityNotFoundException|InvalidArgumentException
     */
    private function setParentCategory(Category $category, int $parentId): void
    {
        $parentCategory = $this->categoryRepository->find($parentId);

        if (!$parentCategory) {
            throw new EntityNotFoundException('Category with ID: ' . $parentId . ' not found', 404);
        }

        if($parentCategory->getId() === $category->getId()) {
            throw new InvalidArgumentException('Category cannot be parent to itself', 409);
        }

        $category->setParent($parentCategory);
    }
}