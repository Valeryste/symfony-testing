<?php

namespace App\Service\Api;

use App\DTO\Api\Admin\Shop\StoreShopDTO;
use App\DTO\Api\Admin\Shop\UpdateShopDTO;
use App\Entity\Shop;
use App\Model\Shop\ShopListResponse;
use App\Model\Shop\ShopResponse;
use App\Repository\CityRepository;
use App\Repository\ShopRepository;
use App\Service\BaseService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class ApiShopService extends BaseService
{
    private const PAGINATION_LIMIT = 10;

    public function __construct(
        private readonly ShopRepository         $shopRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly CityRepository         $cityRepository
    ) {
    }

    public function getList(int $page, array $filters = [], array $sorts = [], array $search = []): ShopListResponse
    {
        if ($this->hasFilter($filters, 'deletedAt', 1)) {
            $this->entityManager->getFilters()->disable('softdeleteable');
        }

        $paginationShops = $this->shopRepository->getPaginatedResults(
            page: $page,
            limit: self::PAGINATION_LIMIT,
            filters: $filters,
            sorts: $sorts,
            search: $search
        );

        if ($this->hasFilter($filters, 'deletedAt', 1)) {
            $this->entityManager->getFilters()->enable('softdeleteable');
        }

        return ShopListResponse::fromPagination($paginationShops);
    }

    public function show(Shop $shop): ShopResponse
    {
        return ShopResponse::fromEntity($shop);
    }

    /**
     * @throws \Exception
     */
    public function store(StoreShopDTO $storeShopDTO): ShopResponse
    {
        $shop = new Shop();

        $shop
            ->setName($storeShopDTO->name)
            ->setIsOpen($storeShopDTO->isOpen)
            ->setAddress($storeShopDTO->address);

        $this->setCityShop($shop, $storeShopDTO->cityId);

        $this->entityManager->persist($shop);

        $this->entityManager->flush();

        return ShopResponse::fromEntity($shop);
    }

    /**
     * @throws \Exception
     */
    public function update(UpdateShopDTO $updateShopDTO, Shop $shop): ShopResponse
    {
        $shop
            ->setName($updateShopDTO->name ?? $shop->getName())
            ->setIsOpen($updateShopDTO->isOpen ?? $shop->isOpen())
            ->setAddress($updateShopDTO->address ?? $shop->getAddress())
            ->setUpdatedAt(new \DateTime());

        if (!empty($updateShopDTO->cityId)) {
           $this->setCityShop($shop, $updateShopDTO->cityId);
        }

        $this->entityManager->flush();

        return ShopResponse::fromEntity($shop);
    }

    public function delete(Shop $shop): void
    {
        $this->entityManager->remove($shop);

        $this->entityManager->flush();
    }

    /**
     * @throws EntityNotFoundException
     */
    private function setCityShop(Shop $shop, int $cityId): void
    {
        if (!($city = $this->cityRepository->find($cityId))) {
            throw new EntityNotFoundException('City with ID: ' . $cityId . ' not found', 404);
        }

        $shop->setCity($city);
    }
}