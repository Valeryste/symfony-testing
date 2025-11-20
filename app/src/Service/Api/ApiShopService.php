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

class ApiShopService extends BaseService
{
    private const PAGINATION_LIMIT = 10;

    public
    function __construct(
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

        return new ShopListResponse(
            currentPage: $paginationShops->getCurrentPageNumber(),
            totalCount: $paginationShops->getTotalItemCount(),
            shops: array_map(
                function ($shop) {
                    return self::toResponse($shop);
                },
                $paginationShops->getItems())
        );
    }

    public static function toResponse(Shop $shop): ShopResponse
    {
        return new ShopResponse(
            id: $shop->getId(),
            name: $shop->getName(),
            address: $shop->getAddress(),
            isOpen: $shop->isOpen(),
            city: ApiCityService::toResponse($shop->getCity()),
            createdAt: $shop->getCreatedAt(),
            updatedAt: $shop->getUpdatedAt()
        );
    }

    public function show(Shop $shop): ShopResponse
    {
        return self::toResponse($shop);
    }

    /**
     * @throws \Exception
     */
    public function store(StoreShopDTO $storeShopDTO): ShopResponse
    {
        $shop = new Shop();

        $shop->setName($storeShopDTO->name);
        $shop->setIsOpen($storeShopDTO->isOpen);
        $shop->setAddress($storeShopDTO->address);

        if (!($city = $this->cityRepository->find($storeShopDTO->cityId))) {
            throw new \Exception('City with ID: ' . $storeShopDTO->cityId . ' does not exist in DB');
        }

        $shop->setCity($city);

        $this->entityManager->persist($shop);

        $this->entityManager->flush();

        return self::toResponse($shop);
    }

    /**
     * @throws \Exception
     */
    public function update(UpdateShopDTO $updateShopDTO, Shop $shop): ShopResponse
    {
        $shop->setName($updateShopDTO->name ?? $shop->getName());
        $shop->setIsOpen($updateShopDTO->isOpen ?? $shop->isOpen());
        $shop->setAddress($updateShopDTO->address ?? $shop->getAddress());
        $shop->setUpdatedAt(new \DateTime());

        if (!($city = $this->cityRepository->find($updateShopDTO->cityId))) {
            throw new \Exception('City with ID: ' . $updateShopDTO->cityId . ' does not exist in DB');
        }

        $shop->setCity($city);


        $this->entityManager->flush();

        return self::toResponse($shop);
    }

    public function delete(Shop $shop): void
    {
        $this->entityManager->remove($shop);

        $this->entityManager->flush();
    }
}