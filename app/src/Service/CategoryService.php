<?php

namespace App\Service;

use App\Entity\Category;
use App\Enum\CategoryFilters;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

class CategoryService extends  BaseService
{
    private const PAGINATION_LIMIT = 10;

    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function getList(int $page, array $filters = [], array $sorts = []): PaginationInterface
    {
        if (isset($filters['with_deleted']) && $filters['with_deleted'] == 1) {
            $this->entityManager->getFilters()->disable('softdeleteable');
        }

        $transformedFilters = array_filter(
            array_map(
                function ($filterCase) use ($filters) {
                    if (isset($filters[$filterCase->value])) {
                        return [$filterCase, $filters[$filterCase->value]];
                    }
                    return null;
                },
                CategoryFilters::getFilterCases()
            )
        );

        $paginationCountries = $this->categoryRepository->getPaginatedResults(
            page: $page,
            limit: self::PAGINATION_LIMIT,
            filters: $transformedFilters,
            sorts: $sorts
        );

        if (isset($filters['with_deleted']) && $filters['with_deleted'] == 1) {
            $this->entityManager->getFilters()->enable('softdeleteable');
        }

        return $paginationCountries;
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
        $this->entityManager->remove($category);

        $this->entityManager->flush();
    }

    public function getAllParents(): array
    {
        return $this->categoryRepository->getAllParents();
    }

    public function getRootCategories() : array
    {
        return $this->categoryRepository->getRootCategories();
    }

    public function getAllExcept(int $id): array
    {
        return $this->categoryRepository->getAllExcept($id);
    }
}