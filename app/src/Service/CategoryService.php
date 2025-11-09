<?php

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

class CategoryService extends BaseService
{
    private const PAGINATION_LIMIT = 10;

    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function getList(int $page, array $filters = [], array $sorts = [], array $search = []): PaginationInterface
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

        return $paginationCategories;
    }

    public function store(Category $category): Category
    {
        $this->entityManager->persist($category);

        $this->entityManager->flush();

        return $category;
    }

    public function update(Category $category): Category
    {
        $category->setUpdatedAt(new \DateTime());

        $this->entityManager->flush();

        return $category;
    }

    public function delete(Category $category): void
    {
        $category->setIsActive(false);

        $this->entityManager->flush();

        $this->entityManager->remove($category);

        $this->entityManager->flush();
    }

    public function getAllParents(): array
    {
        return $this->categoryRepository->getAllParents();
    }

    public function getAllExcept(int $id): array
    {
        return $this->categoryRepository->getAllExcept($id);
    }
}