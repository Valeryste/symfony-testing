<?php

namespace App\Service\Api;

use App\DTO\Api\Admin\Product\StoreProductDTO;
use App\DTO\Api\Admin\Product\UpdateProductDTO;
use App\Entity\Product;
use App\Model\Category\CategoryItemResponse;
use App\Model\Product\ProductItemResponse;
use App\Model\Product\ProductListResponse;
use App\Model\Product\ProductResponse;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\BaseService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class ApiProductService extends BaseService
{
    private const PAGINATION_LIMIT = 10;

    public function __construct(
        private readonly ProductRepository      $productRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly CategoryRepository     $categoryRepository
    ) {
    }

    public function getList(int $page, array $filters = [], array $sorts = [], array $search = []): ProductListResponse
    {
        if ($this->hasFilter($filters, 'deletedAt', 1)) {
            $this->entityManager->getFilters()->disable('softdeleteable');
        }

        $paginationProducts = $this->productRepository->getPaginatedResults(
            page: $page,
            limit: self::PAGINATION_LIMIT,
            filters: $filters,
            sorts: $sorts,
            search:  $search
        );

        if ($this->hasFilter($filters, 'deletedAt', 1)) {
            $this->entityManager->getFilters()->enable('softdeleteable');
        }

        return ProductListResponse::fromPagination($paginationProducts);
    }

    public function show(Product $product): ProductResponse
    {
        return ProductResponse::fromEntity($product);
    }

    public function store(StoreProductDTO $storeProductDTO): ProductResponse
    {
        $product = new Product();

        $product->setName($storeProductDTO->name)
            ->setPrice($storeProductDTO->price)
            ->setCount($storeProductDTO->count);

        if(!empty($storeProductDTO->categoryIds)) {
            $this->setCategoriesProduct($product, $storeProductDTO->categoryIds);
        }

        $this->entityManager->persist($product);

        $this->entityManager->flush();

        return ProductResponse::fromEntity($product);
    }

    public function update(UpdateProductDTO $updateProductDTO, Product $product): ProductResponse
    {
        $product->setName($updateProductDTO->name ?? $product->getName())
            ->setPrice($updateProductDTO->price ?? $product->getPrice())
            ->setCount($updateProductDTO->count ?? $product->getCount())
            ->setIsActive($updateProductDTO->isActive ?? $product->isActive())
            ->setUpdatedAt(new \DateTime());

        if(!empty($updateProductDTO->categoryIds)) {
            $this->setCategoriesProduct($product, $updateProductDTO->categoryIds);
        }

        $this->entityManager->flush();

        return ProductResponse::fromEntity($product);
    }

    public function delete(Product $product): void
    {
        $product->removeAllCategories();

        $this->entityManager->remove($product);

        $this->entityManager->flush();
    }

    /**
     * @throws EntityNotFoundException
     */
    private function setCategoriesProduct(Product $product, array $categoryIds): void
    {
        $foundCategories = $this->categoryRepository->findBy(['id' => $categoryIds]);

        $foundCategoryIds = array_map(fn($category) => $category->getId(), $foundCategories);
        $missingCategoryIds = array_diff($categoryIds, $foundCategoryIds);

        if (!empty($missingCategoryIds)) {
            throw new EntityNotFoundException('Categories with IDs: ' . implode(', ', $missingCategoryIds) . ' not found', 404);
        }

        $product->addCategories($foundCategories);
    }
}