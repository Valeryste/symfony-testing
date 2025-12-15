<?php

namespace App\Service\Web;

use App\Entity\Shop;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use App\Repository\ShopRepository;
use App\Service\BaseService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

class ShopService extends BaseService
{
    private const PAGINATION_LIMIT = 10;

    public
    function __construct(
        private readonly ShopRepository         $shopRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly CityRepository      $cityRepository,
        private readonly CountryRepository $countryRepository
    ) {
    }

    public function getList(int $page, array $filters = [], array $sorts = [], array $search = []): PaginationInterface
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


        return $paginationShops;
    }

    public function getCitiesHavingShops(): array
    {
        return $this->cityRepository->getCitiesHavingShops();
    }

    public function store(Shop $shop): Shop
    {
        $this->entityManager->persist($shop);

        $this->entityManager->flush();

        return $shop;
    }


    public function update(Shop $shop): Shop
    {
        $shop->setUpdatedAt(new \DateTime());

        $this->entityManager->flush();

        return $shop;
    }

    public function delete(Shop $shop): void
    {
        $this->entityManager->remove($shop);

        $this->entityManager->flush();
    }

    public function getCityName(Shop $shop): string
    {
        $this->entityManager->getFilters()->disable('softdeleteable');

        $name = $shop->getCity()->getName();

        $this->entityManager->getFilters()->enable('softdeleteable');

        return $name;
    }

    public function getCountriesHavingShops(): array
    {
        return $this->countryRepository->getCountriesHavingShops();
    }
}