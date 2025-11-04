<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

class ProductService extends BaseService
{
    private const PAGINATION_LIMIT = 10;

    public function __construct(
        private readonly ProductRepository      $productRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly CategoryRepository     $categoryRepository
    )
    {
    }


    public function getList(int $page, array $filters = [], array $sorts = []): PaginationInterface
    {
        if ($this->hasFilter($filters, 'deletedAt', 1)) {
            $this->entityManager->getFilters()->disable('softdeleteable');
        }

        $paginationCountries = $this->productRepository->getPaginatedResults(
            page: $page,
            limit: self::PAGINATION_LIMIT,
            filters: $filters,
            sorts: $sorts
        );

        if ($this->hasFilter($filters, 'deletedAt', 1)) {
            $this->entityManager->getFilters()->enable('softdeleteable');
        }

        return $paginationCountries;
    }


    public function store(Product $product): Product
    {
        $product->setIsActive(false);

        $this->entityManager->flush();

        $this->entityManager->persist($product);

        $this->entityManager->flush();

        return $product;
    }

    public function update(Product $product): Product
    {
        $product->setUpdatedAt(new \DateTime());

        $this->entityManager->flush();

        return $product;
    }

    public function delete(Product $product): void
    {
        $product->removeAllCategories();

        $this->entityManager->remove($product);

        $this->entityManager->flush();
    }

    public function finCategoriesWithProducts(): array
    {
        return $this->categoryRepository->findCategoriesWithProducts();
    }
}