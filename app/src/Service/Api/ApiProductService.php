<?php

namespace App\Service\Api;

use App\DTO\Api\Admin\Product\StoreProductDTO;
use App\DTO\Api\Admin\Product\UpdateProductDTO;
use App\Entity\Product;
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

        return new ProductListResponse(
            currentPage: $paginationProducts->getCurrentPageNumber(),
            totalCount: $paginationProducts->getTotalItemCount(),
            products: array_map(function ($product){
                return new ProductItemResponse(
                    id: $product->getId(),
                    name: $product->getName(),
                    price: $product->getPrice()
                );
            }, $paginationProducts->getItems())
        );
    }

    private static function toResponse(Product $product): ProductResponse
    {
        return new ProductResponse(
            id: $product->getId(),
            name: $product->getName(),
            price: $product->getPrice(),
            count: $product->getCount(),
            isActive: $product->isActive(),
            createdAt: $product->getCreatedAt(),
            updatedAt: $product->getUpdatedAt()
        );
    }

    public function show(Product $product): ProductResponse
    {
        return self::toResponse($product);
    }

    public function store(StoreProductDTO $storeProductDTO): ProductResponse
    {
        $product = new Product();

        $product->setName($storeProductDTO->name);
        $product->setPrice($storeProductDTO->price);
        $product->setCount($storeProductDTO->count);
        if(!empty($storeProductDTO->categoryIds)) {
            $this->setCategoriesProduct($product, $storeProductDTO->categoryIds);
        }

        $this->entityManager->persist($product);

        $this->entityManager->flush();

        return self::toResponse($product);
    }

    public function update(UpdateProductDTO $updateProductDTO, Product $product): ProductResponse
    {
        $product->setName($updateProductDTO->name ?? $product->getName());
        $product->setPrice($updateProductDTO->price ?? $product->getPrice());
        $product->setCount($updateProductDTO->count ?? $product->getCount());
        $product->setIsActive($updateProductDTO->isActive ?? $product->isActive());
        if(!empty($updateProductDTO->categoryIds)) {
            $this->setCategoriesProduct($product, $updateProductDTO->categoryIds);
        }
        $product->setUpdatedAt(new \DateTime());

        $this->entityManager->flush();

        return self::toResponse($product);
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