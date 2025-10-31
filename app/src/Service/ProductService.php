<?php

namespace App\Service;

use App\Entity\Product;
use App\Enum\ProductFilters;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

class ProductService extends BaseService
{
    private const PAGINATION_LIMIT = 10;

    public function __construct(
        private readonly ProductRepository      $productRepository,
        private readonly EntityManagerInterface $entityManager
    )
    {
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
                ProductFilters::getFilterCases()
            )
        );

        $paginationCountries = $this->productRepository->getPaginatedResults(
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


    public function store(Product $product): Product
    {
        $this->entityManager->beginTransaction();

        try {

            $this->entityManager->persist($product);

            $this->entityManager->flush();

            $this->entityManager->commit();

        } catch (\Exception $e) {
            $this->entityManager->rollBack();
            throw new \RuntimeException('Failed to save product: ' . $e->getMessage(), 0, $e);
        }

        return $product;
    }

    public function update(Product $product): Product
    {
        $product->setUpdatedAt(new \DateTime());

        $this->entityManager->flush();

        $this->entityManager->commit();

        return $product;
    }

    public function delete(Product $product): void
    {
        $product->removeAllCategories();

        $this->entityManager->remove($product);

        $this->entityManager->flush();

        $this->entityManager->commit();

    }
}