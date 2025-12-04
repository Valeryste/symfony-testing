<?php

namespace App\Service\Api;

use App\Model\CountHistory\CountHistoryListResponse;
use App\Repository\CountHistoryRepository;
use App\Repository\ProductRepository;
use App\Service\BaseService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class ApiPriceHistoryService extends BaseService
{
    private const PAGINATION_LIMIT = 10;

    public function __construct(
        private readonly CountHistoryRepository $countHistoryRepository,
        private readonly ProductRepository      $productRepository,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function getListByProduct(int $productId, int $page, array $filters = [], array $sorts = [], array $search = []): CountHistoryListResponse
    {
        if ($this->hasFilter($filters, 'deletedAt', 1)) {
            $this->entityManager->getFilters()->disable('softdeleteable');
        }

        $paginationPriceHistories = $this->countHistoryRepository->getPaginatedResults(
            page: $page,
            limit: self::PAGINATION_LIMIT,
            filters: $filters,
            sorts: $sorts,
            search: $search,
            product: $this->getProduct($productId)
        );

        if ($this->hasFilter($filters, 'deletedAt', 1)) {
            $this->entityManager->getFilters()->enable('softdeleteable');
        }

        return CountHistoryListResponse::fromPagination($paginationPriceHistories);
    }

    private function getProduct(int $productId)
    {
        if (!$this->productRepository->find($productId)) {
            throw new EntityNotFoundException('Product with ID: ' . $productId . ' not found', 404);
        }
        return $this->productRepository->find($productId);
    }
}